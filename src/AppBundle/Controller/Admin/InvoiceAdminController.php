<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Invoice;
use AppBundle\Pdf\InvoiceBuilderPdf;
use AppBundle\Service\NotificationService;
use AppBundle\Service\XmlSepaBuilderService;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class InvoiceAdminController.
 *
 * @category Controller
 */
class InvoiceAdminController extends BaseAdminController
{
    /**
     * Generate invoice action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException    If the object does not exist
     * @throws AccessDeniedException    If access is not granted
     * @throws NonUniqueResultException If problem with unique entities
     *
    public function generateAction(Request $request = null)
    {
        /** @var GenerateInvoiceFormManager $gifm *
        $gifm = $this->container->get('app.generate_invoice_form_manager');

        // year & month chooser form
        $generateInvoiceYearMonthChooser = new GenerateInvoiceModel();
        /** @var Controller $this *
        $yearMonthForm = $this->createForm(GenerateInvoiceYearMonthChooserType::class, $generateInvoiceYearMonthChooser);
        $yearMonthForm->handleRequest($request);

        // build items form
        $generateInvoice = new GenerateInvoiceModel();
        /** @var Controller $this *
        $form = $this->createForm(GenerateInvoiceType::class, $generateInvoice);
        $form->handleRequest($request);

        if ($yearMonthForm->isSubmitted() && $yearMonthForm->isValid()) {
            $year = $generateInvoiceYearMonthChooser->getYear();
            $month = $generateInvoiceYearMonthChooser->getMonth();
            // fill full items form
            $generateInvoice = $gifm->buildFullModelForm($year, $month);
            /** @var Controller $this *
            $form = $this->createForm(GenerateInvoiceType::class, $generateInvoice);
        }

        return $this->renderWithExtraParams(
            '::Admin/Invoice/generate_invoice_form.html.twig',
            array(
                'action' => 'generate',
                'year_month_form' => $yearMonthForm->createView(),
                'form' => $form->createView(),
                'generate_invoice' => $generateInvoice,
            )
        );
    }

    /**
     * Creator invoice action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException                 If the object does not exist
     * @throws AccessDeniedException                 If access is not granted
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
    public function creatorAction(Request $request = null)
    {
        /** @var Translator $translator *
        $translator = $this->container->get('translator.default');
        /** @var GenerateInvoiceFormManager $gifm *
        $gifm = $this->container->get('app.generate_invoice_form_manager');
        $generateInvoice = $gifm->transformRequestArrayToModel($request->get('generate_invoice'));

        $recordsParsed = $gifm->persistFullModelForm($generateInvoice);
        if (0 === $recordsParsed) {
            $this->addFlash('warning', $translator->trans('backend.admin.invoice.generator.no_records_presisted'));
        } else {
            $this->addFlash('success', $translator->trans('backend.admin.invoice.generator.flash_success', array('%amount%' => $recordsParsed), 'messages'));
        }

        return $this->redirectToList();
    }*/

    /**
     * Generate PDF invoice action.
     *
     * @param Request         $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function pdfAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Invoice $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        /** @var InvoiceBuilderPdf $ibp */
        $ibp = $this->container->get('app.invoice_builder_pdf');
        $pdf = $ibp->build($object);

        return new Response($pdf->Output('espai_kowo_invoice_'.$object->getUnderscoredInvoiceNumber().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
    }

    /**
     * Send PDF invoice action.
     *
     * @param Request         $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Invoice $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $em = $this->container->get('doctrine')->getManager();
        $object
            ->setIsSended(true)
            ->setSendDate(new \DateTime())
        ;
        $em->flush();

        /** @var InvoiceBuilderPdf $ibp */
        $ibp = $this->container->get('app.invoice_builder_pdf');
        $pdf = $ibp->build($object);

        /** @var NotificationService $ns */
        $ns = $this->container->get('app.notification');
        $result = $ns->sendInvoicePdfNotification($object, $pdf);

        if (0 === $result) {
            $this->addFlash('danger', 'S\'ha produït un error durant l\'enviament de la factura núm. '.$object->getInvoiceNumber().'. El client '.$object->getCustomer().' no ha rebut cap missatge a la seva bústia.');
        } else {
            $this->addFlash('success', 'S\'ha enviat la factura núm. '.$object->getInvoiceNumber().' amb PDF a la bústia '.$object->getCustomer()->getEmail().' del client '.$object->getCustomer());
        }

        return $this->redirectToList();
    }

    /**
     * Generate XML SEPA direct debit invoice action.
     *
     * @param Request $request
     *
     * @return Response|BinaryFileResponse
     *
     * @throws \Digitick\Sepa\Exception\InvalidArgumentException
     * @throws \Digitick\Sepa\Exception\InvalidPaymentMethodException
     */
    public function xmlAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Invoice $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        /** @var XmlSepaBuilderService $xsbs */
        $xsbs = $this->container->get('app.direct_debit_builder_xml');
        $paymentUniqueId = uniqid();
        $xml = $xsbs->buildDirectDebitSingleInvoiceXml($paymentUniqueId, new \DateTime('now + 4 days'), $object);

        if ($this->getParameter('kernel.environment') == 'dev') {
            return new Response($xml, 200, array('Content-type' => 'application/xml'));
        }

        $now = new \DateTime();
        $fileSystem = new Filesystem();
        $fileNamePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'SEPA_invoice_'.$now->format('Y-m-d').'___'.$paymentUniqueId.'.xml';
        $fileSystem->touch($fileNamePath);
        $fileSystem->dumpFile($fileNamePath, $xml);

        $response = new BinaryFileResponse($fileNamePath, 200, array('Content-type' => 'application/xml'));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     *
     * @return Response|BinaryFileResponse
     */
    public function batchActionGeneratesepaxmls(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');

        $selectedModels = $selectedModelQuery->execute();
        try {
            /** @var XmlSepaBuilderService $xsbs */
            $xsbs = $this->container->get('app.direct_debit_builder_xml');
            $paymentUniqueId = uniqid();
            $xmls = $xsbs->buildDirectDebitInvoicesXml($paymentUniqueId, new \DateTime('now + 3 days'), $selectedModels);

            if ($this->getParameter('kernel.environment') == 'dev') {
                return new Response($xmls, 200, array('Content-type' => 'application/xml'));
            }

            $now = new \DateTime();
            $fileSystem = new Filesystem();
            $fileNamePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'SEPA_invoices_'.$now->format('Y-m-d').'___'.$paymentUniqueId.'.xml';
            $fileSystem->touch($fileNamePath);
            $fileSystem->dumpFile($fileNamePath, $xmls);

            $response = new BinaryFileResponse($fileNamePath, 200, array('Content-type' => 'application/xml'));
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

            return $response;

        } catch (\Exception $e) {
            $this->addFlash('error', 'S\'ha produït un error al generar l\'arxiu SEPA amb format XML. Revisa les factures seleccionades.');
            $this->addFlash('error', $e->getMessage());

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }
    }
}
