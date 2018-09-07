<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceLine;
use AppBundle\Pdf\InvoiceBuilderPdf;
use AppBundle\Service\NotificationService;
use AppBundle\Service\XmlSepaBuilderService;
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
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
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
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
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
     * Duplicate an invoice for next month action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws \Exception
     */
    public function duplicateAction(Request $request)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Invoice $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $em = $this->container->get('doctrine')->getManager();
        /** @var Invoice $lastInvoice */
        $lastInvoice = $em->getRepository('AppBundle:Invoice')->getLastInvoice();

        // new invoice
        $number = $lastInvoice->getNumber() + 1;
        $year = $object->getYear();
        $date = $object->getDate();
        $date->add(new \DateInterval('P1M'));
        if (intval($date->format('Y')) == $lastInvoice->getYear() + 1) {
            // new year step found, try to determine if there are other invoices recorded with this year
            $number = 1;
            $year++;
        }
        if ($lastInvoice->getDate()->getTimestamp() > $date->getTimestamp()) {
            $date = $lastInvoice->getDate();
            $year = $lastInvoice->getYear();
        }

        $newInvoice = new Invoice();
        $newInvoice
            ->setNumber($number)
            ->setYear($year)
            ->setDate($date)
            ->setCustomer($object->getCustomer())
            ->setTaxPercentage($object->getTaxPercentage())
            ->setIrpfPercentage($object->getIrpfPercentage())
            ->setBaseAmount($object->getBaseAmount())
            ->setTotalAmount($object->getTotalAmount())
            ->setPaymentMethod($object->getCustomer()->getPaymentMethod())
            ->setIsSended(false)
            ->setIsSepaXmlGenerated(false)
            ->setIsPayed(false)
        ;
        $em->persist($newInvoice);
        $em->flush();

        // new invoice lines
        /** @var InvoiceLine $objectLine */
        foreach ($object->getLines() as $objectLine) {
            $newInvoiceLine = new InvoiceLine();
            $newInvoiceLine
                ->setInvoice($newInvoice)
                ->setDescription($objectLine->getDescription())
                ->setUnits($objectLine->getUnits())
                ->setPriceUnit($objectLine->getPriceUnit())
                ->setDiscount($objectLine->getDiscount())
                ->setTotal($objectLine->getTotal())
            ;
            $em->persist($newInvoiceLine);
            $em->flush();
        }

        $this->addFlash('success', 'S\'ha duplicat la factura núm. '.$object->getInvoiceNumber().' amb la factura núm. '.$newInvoice->getInvoiceNumber().' correctament.');

        return $this->redirectToList();
    }

    /**
     * Generate SEPA direct debit XML action.
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
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        /** @var XmlSepaBuilderService $xsbs */
        $xsbs = $this->container->get('app.direct_debit_builder_xml');
        $paymentUniqueId = uniqid();
        $xml = $xsbs->buildDirectDebitSingleInvoiceXml($paymentUniqueId, new \DateTime('first day of next month'), $object);

        $em = $this->container->get('doctrine')->getManager();
        $object
            ->setIsSepaXmlGenerated(true)
            ->setSepaXmlGenerationDate(new \DateTime())
        ;
        $em->flush();

        if ($this->getParameter('kernel.environment') == 'dev') {
            return new Response($xml, 200, array('Content-type' => 'application/xml'));
        }

        $now = new \DateTime();
        $fileSystem = new Filesystem();
        $fileNamePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'SEPA_invoice_'.$now->format('Y-m-d_H-i').'___'.$paymentUniqueId.'.xml';
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
        $em = $this->container->get('doctrine')->getManager();
        $selectedModels = $selectedModelQuery->execute();

        try {
            /** @var XmlSepaBuilderService $xsbs */
            $xsbs = $this->container->get('app.direct_debit_builder_xml');
            $paymentUniqueId = uniqid();
            $xmls = $xsbs->buildDirectDebitInvoicesXml($paymentUniqueId, new \DateTime('first day of next month'), $selectedModels);

            /** @var Invoice $selectedModel */
            foreach ($selectedModels as $selectedModel) {
                $selectedModel
                    ->setIsSepaXmlGenerated(true)
                    ->setSepaXmlGeneratedDate(new \DateTime())
                ;
            }
            $em->flush();

            if ($this->getParameter('kernel.environment') == 'dev') {
                return new Response($xmls, 200, array('Content-type' => 'application/xml'));
            }

            $now = new \DateTime();
            $fileSystem = new Filesystem();
            $fileNamePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'SEPA_invoices_'.$now->format('Y-m-d_H-i').'___'.$paymentUniqueId.'.xml';
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
                    'filter' => $this->admin->getFilterParameters(),
                ])
            );
        }
    }
}
