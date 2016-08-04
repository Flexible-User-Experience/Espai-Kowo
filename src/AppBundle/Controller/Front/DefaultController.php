<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactMessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="front_homepage")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render(':Frontend:homepage.html.twig', array());
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
            $this->addFlash(
                'notice',
                'El teu missatge s\'ha enviat correctament'
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($contactMessage);

            $em->flush();

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

        }

        return $this->render(':Frontend:contact.html.twig', array(
            'formContact' => $form->createView(),
        ));
    }
}
