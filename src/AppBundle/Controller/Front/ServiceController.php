<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactHomepageType;
use AppBundle\Manager\MailchimpManager;
use AppBundle\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceController
 *
 * @category Controller
 */
class ServiceController extends Controller
{
    /**
     * @Route("/serveis", name="front_services")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function servicesAction(Request $request)
    {
        $contact = new ContactMessage();
        $form = $this->createForm(ContactHomepageType::class, $contact);
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
            // Subscribe contact to free-trial mailchimp list
            $mailchimpManager->subscribeContactToList($contact, $this->getParameter('mailchimp_free_trial_list_id'));
            // Send email notifications
            $messenger->sendCommonUserNotification($contact);
            $messenger->sendNewsletterSubscriptionAdminNotification($contact, 'serveis');
            // Clean up new form
            $form = $this->createForm(ContactHomepageType::class);
        }

        return $this->render(':Frontend:Service/services.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
