<?php

namespace AppBundle\Service;

use AppBundle\Entity\ContactMessage;
use AppBundle\Entity\Coworker;

/**
 * Class NotificationService
 *
 * @category Service
 * @package  AppBundle\Service
 * @author   David Romaní <david@flux.cat>
 */
class NotificationService
{
    /** @var CourierService */
    private $messenger;

    /** @var \Twig_Environment */
    private $twig;

    /** @var string */
    private $amd;

    /** @var string */
    private $urlBase;

    /**
     * NotificationService constructor
     *
     * @param CourierService    $messenger
     * @param \Twig_Environment $twig
     * @param string            $amd
     */
    public function __construct(CourierService $messenger, \Twig_Environment $twig, $amd, $urlBase)
    {
        $this->messenger = $messenger;
        $this->twig = $twig;
        $this->amd = $amd;
        $this->urlBase = $urlBase;
    }

    /**
     * Send a contact form notification to administrator
     *
     * @param ContactMessage $contactMessage
     */
    public function sendAdminNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $contactMessage->getEmail(),
            $this->amd,
            $this->urlBase . ' contact form received',
            $this->twig->render(':Mails:contact_form_admin_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
        );
    }

    /**
     * Send a contact form notification to web user
     *
     * @param ContactMessage $contactMessage
     */
    public function sendUserNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            $this->urlBase . ' question received',
            $this->twig->render(':Mails:contact_form_user_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
        );
    }

    /**
     * Send backend answer notification to web user
     *
     * @param ContactMessage $contactMessage
     */
    public function senddUserBackendNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            $this->urlBase . ' contact form answer',
            $this->twig->render(':Mails:contact_form_user_backend_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
        );
    }

    /**
     * Send coworker birthday notification to web user
     *
     * @param Coworker $coworker
     */
    public function sendCoworkerBirthdayNotification(Coworker $coworker)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $coworker->getEmail(),
            $this->urlBase . ' contact form answer',
            $this->twig->render(':Mails:coworker_birthday_congratulation_notification.html.twig', array(
                'coworker' => $coworker,
            ))
        );
    }
}
