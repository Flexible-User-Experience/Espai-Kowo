<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Entity\Event;
use AppBundle\Entity\EventCategory;
use AppBundle\Form\Type\ContactNewsletterType;
use AppBundle\Manager\MailchimpManager;
use AppBundle\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EventController
 *
 * @category Controller
 */
class EventController extends Controller
{
    /**
     * @Route("/activitats/{pagina}", name="front_events_list", options={"i18n"=false})
     *
     * @param Request $request
     * @param int $pagina
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function listAction(Request $request, $pagina = 1)
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:EventCategory')->getAllEnabledSortedByTitle();
        $allEvents = $this->getDoctrine()->getRepository('AppBundle:Event')->findAllEnabledSortedByDate();
        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->remove('recaptcha');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            $form->remove('recaptcha');
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($allEvents, $pagina, 9);
        $newEvents = array();
        $oldEvents = array();
        $now = new \DateTime();
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
            [
                'form' => $form->createView(),
                'pagination' => $pagination,
                'oldEvents' => $oldEvents,
                'newEvents' => $newEvents,
                'categories' => $categories,
            ]
        );
    }

    /**
     * @Route("/activitat/{slug}", name="front_event_detail", options={"i18n"=false})
     *
     * @param Request $request
     * @param string $slug
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws NotFoundHttpException
     */
    public function detailAction(Request $request, $slug)
    {
        /** @var Event $event */
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(
            array(
                'slug' => $slug,
            )
        );
        if (!$event->getEnabled()) {
            throw new NotFoundHttpException();
        }
        $categories = $this->getDoctrine()->getRepository('AppBundle:EventCategory')->getAllEnabledSortedByTitle();

        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->remove('recaptcha');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            $form->remove('recaptcha');
        }

        return $this->render(':Frontend/Event:detail.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
            'categories' => $categories,
            )
        );
    }

    /**
     * @Route("/activitat/categoria/{slug}/{pagina}", name="front_category_event", options={"i18n"=false})
     *
     * @param Request $request
     * @param string $slug
     * @param int $pagina
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function categoryEventAction(Request $request, $slug, $pagina = 1)
    {
        /** @var EventCategory $category */
        $category = $this->getDoctrine()->getRepository('AppBundle:EventCategory')->findOneBy(
            array(
                'slug' => $slug,
            )
        );
        if (!$category || !$category->getEnabled()) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $allEvents = $this->getDoctrine()->getRepository('AppBundle:Event')->getEventsByCategoryEnabledSortedByDateWithJoin($category);
        $categories = $this->getDoctrine()->getRepository('AppBundle:EventCategory')->getAllEnabledSortedByTitle();

        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->remove('recaptcha');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            $form->remove('recaptcha');
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($allEvents, $pagina, 9);
        $newEvents = array();
        $oldEvents = array();
        $now = new \DateTime();
        /** @var Event $event */
        foreach ($pagination as $event) {
            if ($event->getDate()->format('Y-m-d') >= $now->format('Y-m-d')) {
                $newEvents[] = $event;
            } else {
                $oldEvents[] = $event;
            }
        }

        return $this->render(':Frontend/Event:category_detail.html.twig', array(
            'selectedCategory' => $category,
            'categories' => $categories,
            'form' => $form->createView(),
            'pagination' => $pagination,
            'oldEvents' => $oldEvents,
            'newEvents' => $newEvents,
        ));
    }

    /**
     * @param ContactMessage $contact
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function setFlashMailchimpSubscribeAndEmailNotifications($contact)
    {
        /** @var MailchimpManager $mailchimpManager */
        $mailchimpManager = $this->get('app.mailchimp_manager');
        /** @var NotificationService $messenger */
        $messenger = $this->get('app.notification');
        // Subscribe contact to free-trial mailchimp list
        $userSubscriptionResult = $mailchimpManager->subscribeContactToList($contact, $this->getParameter('mailchimp_newsletter_list_id'));
        // Send email notifications
        $adminDeliveryResult = $messenger->sendNewsletterSubscriptionAdminNotification($contact, 'activitats');
        // Set frontend flash message
        if ($userSubscriptionResult === true && $adminDeliveryResult > 0) {
            $this->addFlash(
                'notice',
                'Gràcies per registrar-te al newsletter.'
            );
        } else {
            $this->addFlash(
                'danger',
                'Ho sentim, s\'ha produït un error a durant el procés de registre al newsletter. Torna a intentar-ho.'
            );
        }
    }
}
