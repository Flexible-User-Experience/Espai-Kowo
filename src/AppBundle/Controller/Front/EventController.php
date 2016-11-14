<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Entity\Event;
use AppBundle\Form\Type\ContactNewsletterType;
use AppBundle\Manager\MailchimpManager;
use AppBundle\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    /**
     * @Route("/activitats/{pagina}", name="front_events_list")
     *
     * @param Request $request
     * @param int     $pagina
     *
     * @return Response
     */
    public function listAction(Request $request, $pagina = 1)
    {
        $allEvents = $this->getDoctrine()->getRepository('AppBundle:Event')->findAllEnabledSortedByDate();
        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($allEvents, $pagina);
        $newEvents = array(); $oldEvents = array(); $now = new \DateTime();
        /** @var Event $event */
        foreach ($pagination as $event) {
            if ($event->getDate()->format('Y-m-d') >= $now->format('Y-m-d')) {
                $newEvents[] = $event;
            } else {
                $oldEvents[] = $event;
            }
        }

        return $this->render(
            ':Frontend/Event:list.html.twig',
            [ 'form' => $form->createView(), 'pagination' => $pagination, 'oldEvents' => $oldEvents, 'newEvents' => $newEvents ]
        );
    }

    /**
     * @Route("/activitat/{slug}", name="front_event_detail")
     *
     * @param Request $request
     * @param string $slug
     *
     * @return Response
     */
    public function detailAction(Request $request, $slug)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(
            array(
                'slug' => $slug,
            )
        );

        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
        }

        return $this->render(
            ':Frontend/Event:detail.html.twig',
            [ 'event' => $event, 'form' => $form->createView(), ]
        );
    }

    /**
     * @param ContactMessage $contact
     */
    private function setFlashMailchimpSubscribeAndEmailNotifications($contact)
    {
        /** @var MailchimpManager $mailchimpManager */
        $mailchimpManager = $this->get('app.mailchimp_manager');
        /** @var NotificationService $messenger */
        $messenger = $this->get('app.notification');
        // Set frontend flash message
        $this->addFlash(
            'notice',
            'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
        );
        // Subscribe contact to free-trial mailchimp list
        $mailchimpManager->subscribeContactToList($contact, $this->getParameter('mailchimp_newsletter_list_id'));
        // Send email notifications
        $messenger->sendCommonUserNotification($contact);
        $messenger->sendNewsletterSubscriptionAdminNotification($contact);
    }
}
