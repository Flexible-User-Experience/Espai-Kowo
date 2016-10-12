<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactHomepageType;
use AppBundle\Form\Type\ContactMessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="front_homepage")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(ContactHomepageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set frontend flash message
            $this->addFlash(
                'notice',
                'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
            );
            // Send email notifications
            $message = \Swift_Message::newInstance()
                ->setSubject('Missatge de contacte pàgina web ' . $this->getParameter('mailer_url_base'))
                ->setFrom($this->getParameter('mailer_destination'))
                ->setTo($this->getParameter('mailer_destination'))
                ->setBody(
                    $this->renderView(
                        ':Frontend/Mail:contact_form_admin_notification.html.twig',
                        array('contact' => $form->getData())
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
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
     */
    public function contactAction(Request $request)
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set frontend flash message
            $this->addFlash(
                'notice',
                'El teu missatge s\'ha enviat correctament'
            );
            // Persist new contact message into DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($contactMessage);
            $em->flush();
            // Send email notifications
            $message = \Swift_Message::newInstance()
                ->setSubject('Missatge de contacte pàgina web espaikowo.cat')
                ->setFrom($contactMessage->getEmail())
                ->setTo('info@espaikowo.cat')
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        ':Mails:contact_form_admin_notification.html.twig',
                        array('contact' => $contactMessage)
                    ),
                    'text/html'
                )

//                ->addPart(
//                    $this->renderView(
//                        ':Mails:',
//                        array('name' => $name)
//                    ),
//                    'text/plain'
//                )

            ;
            $this->get('mailer')->send($message);
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
     * @throws NotFoundHttpException
     */
    public function testEmailAction()
    {
        if ($this->container->get('kernel')->getEnvironment() == 'prod') {
            throw new NotFoundHttpException();
        }

        return $this->render(':Mails:free_trial_user_notification.html.twig', array());
    }
}
