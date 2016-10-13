<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactNewsletterType;
use AppBundle\Service\NotificationService;
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
            // Set frontend flash message
            $this->addFlash(
                'notice',
                'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
            );
            // Save Mailchimp or Sendgrid user to list

            // (to do...)

            // Send email notifications
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            $messenger->sendCommonUserNotification($contact);
            $messenger->sendNewsletterSubscriptionAdminNotification($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
        }

        return $this->render(
            ':Frontend/Event:list.html.twig',
            [ 'events' => $events, 'form' => $form->createView(), ]
        );
    }

    /**
     * @Route("/activitat/{slug}", name="front_event_detail")
     *
     * @param $slug
     * @return Response
     */
    public function detailAction($slug)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(
            array(
                'slug' => $slug,
            )
        );

        return $this->render(
            ':Frontend/Event:detail.html.twig',
            [ 'event' => $event ]
        );
    }
}
