<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactHomepageType;
use AppBundle\Form\Type\ContactMessageType;
use AppBundle\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DefaultController
 *
 * @category Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="front_homepage")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function indexAction(Request $request)
    {
        $contact = new ContactMessage();
        $form = $this->createForm(ContactHomepageType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            // Send email notifications
            $userDeliveryResult = $messenger->sendCommonUserNotification($contact);
            $adminDeliveryResult = $messenger->sendCommonContactAdminNotification($contact, 'homepage');
            // Set frontend flash message
            if ($userDeliveryResult > 0 && $adminDeliveryResult > 0) {
                $this->addFlash(
                    'notice',
                    'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Ho sentim, s\'ha produït un error a l\'enviar el missatge de contacte. Torna a intentar-ho.'
                );
            }
            // Clean up new form
            $form = $this->createForm(ContactHomepageType::class);
        }

        return $this->render(':Frontend:homepage.html.twig', array(
            'formHomepage' => $form->createView(),
        ));
    }

    /**
     * @Route("/contacte", name="front_contact")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function contactAction(Request $request)
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist new contact message into DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($contactMessage);
            $em->flush();
            // Send email notifications
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            $userDeliveryResult = $messenger->sendCommonUserNotification($contactMessage);
            $adminDeliveryResult = $messenger->sendContactAdminNotification($contactMessage);
            // Set frontend flash message
            if ($userDeliveryResult > 0 && $adminDeliveryResult > 0) {
                $this->addFlash(
                    'notice',
                    'El teu missatge s\'ha enviat correctament, ens posarem en contacte amb tu el més aviat possible. Gràcies.'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Ho sentim, s\'ha produït un error a l\'enviar el missatge de contacte. Torna a intentar-ho.'
                );
            }
            // Clean up new form
            $contactMessage = new ContactMessage();
            $form = $this->createForm(ContactMessageType::class, $contactMessage);
        }

        return $this->render(':Frontend:contact.html.twig', array(
            'formContact' => $form->createView(),
        ));
    }

    /**
     * @Route("/politica-de-privacitat", name="front_privacy_policy")
     *
     * @return Response
     */
    public function privacyPolicyAction()
    {
        return $this->render(':Frontend:privacy_policy.html.twig', array());
    }

    /**
     * @Route("/credits", name="front_credits")
     *
     * @return Response
     */
    public function creditsAction()
    {
        return $this->render(':Frontend:credits.html.twig');
    }

    /**
     * @Route("/test-email", name="front_test_email")
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function testEmailAction()
    {
        if ($this->container->get('kernel')->getEnvironment() == 'prod') {
            throw new NotFoundHttpException();
        }

        $contactMessage = $this->getDoctrine()->getRepository('AppBundle:ContactMessage')->find(1);

        return $this->render(':Mails:user_backend_answer_notification.html.twig', array('contact' => $contactMessage));
    }
}
