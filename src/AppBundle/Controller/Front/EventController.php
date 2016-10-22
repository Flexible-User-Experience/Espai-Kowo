<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactNewsletterType;
use AppBundle\Manager\MailchimpManager;
use AppBundle\Service\NotificationService;
use MZ\MailChimpBundle\Services\MailChimp;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    /**
     * @Route("/activitats", name="front_events_list")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findAllEnabledSortedByDate();
        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MailchimpManager $mailchimpManager */
            $mailchimpManager = $this->get('app.mailchimp_manager');
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            // Set frontend flash message
            $this->addFlash(
                'notice',
                'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
            );
            // Check contact to list
            $result= $mailchimpManager->subscribeContactToList($contact, $this->getParameter('mailchimp_newsletter_list_id'));
            if ($result == false) {
                $messenger->sendCommonAdminNotification('En ' . $contact->getEmail() . ' no s\'ha pogut registrar a la llista de Mailchimp');
            }
            // Send email notifications
            $messenger->sendCommonUserNotification($contact);
            $messenger->sendNewsletterSubscriptionAdminNotification($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            //TODO flashmessage condicionatS
        }

        return $this->render(
            ':Frontend/Event:list.html.twig',
            [ 'events' => $events, 'form' => $form->createView(), ]
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
            /** @var MailChimp $mailchimpService */
            $mailchimpManager = $this->get('app.mailchimp_manager');
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            // Set frontend flash message
            $this->addFlash(
                'notice',
                'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
            );
            // Subscribe contact list and check
            $result = $mailchimpManager->subscribeContactToList($contact, $this->getParameter('mailchimp_newsletter_list_id'));
            if ($result == false) {
                $messenger->sendCommonAdminNotification('En ' . $contact->getEmail() . ' no s\'ha pogut registrar a la llista de Mailchimp');
            }
            // Send email notifications
            $messenger->sendCommonUserNotification($contact);
            $messenger->sendNewsletterSubscriptionAdminNotification($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            //TODO flashmessage condicionat
        }

        return $this->render(
            ':Frontend/Event:detail.html.twig',
            [ 'event' => $event, 'form' => $form->createView(), ]
        );
    }
}
