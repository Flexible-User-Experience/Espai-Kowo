<?php

namespace AppBundle\Service;

use AppBundle\Entity\ContactMessage;
use AppBundle\Entity\Coworker;

/**
 * Class NotificationService.
 *
 * @category Service
 *
 * @author   David Romaní <david@flux.cat>
 */
class NotificationService
{
    /**
     * @var CourierService
     */
    private $messenger;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $amd;

    /**
     * @var string
     */
    private $urlBase;

    /**
     * Methods.
     */

    /**
     * NotificationService constructor.
     *
     * @param CourierService    $messenger
     * @param \Twig_Environment $twig
     * @param string            $amd
     * @param string            $urlBase
     */
    public function __construct(CourierService $messenger, \Twig_Environment $twig, $amd, $urlBase)
    {
        $this->messenger = $messenger;
        $this->twig = $twig;
        $this->amd = $amd;
        $this->urlBase = $urlBase;
    }

    /**
     * Send a common notification mail to frontend user.
     *
     * @param ContactMessage $contactMessage
     */
    public function sendCommonUserNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            'Notificació pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:common_user_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
        );
    }

    /**
     * Send a common notification mail to admin user.
     *
     * @param string $text
     */
    public function sendCommonAdminNotification($text)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Notificació pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:common_admin_notification.html.twig', array(
                'text' => $text,
            ))
        );
    }

    /**
     * Send free trial notification to admin user.
     *
     * @param ContactMessage $contactMessage
     */
    public function sendFreeTrialAdminNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Missatge de prova-ho gratis pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:free_trial_admin_notification.html.twig', array(
                'contact' => $contactMessage,
            )),
            $contactMessage->getEmail()
        );
    }

    /**
     * Send a contact form notification to admin user.
     *
     * @param ContactMessage $contactMessage
     */
    public function sendContactAdminNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Missatge de contacte pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:contact_form_admin_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
        );
    }

    /**
     * Send a contact form notification to admin user.
     *
     * @param ContactMessage $contactMessage
     */
    public function sendUserBackendAnswerNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $contactMessage->getEmail(),
            'Resposta pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:user_backend_answer_notification.html.twig', array(
                'contact' => $contactMessage,
            ))
        );
    }

    /**
     * Send a newsletter subscription form notification to admin user.
     *
     * @param ContactMessage $contactMessage
     */
    public function sendNewsletterSubscriptionAdminNotification(ContactMessage $contactMessage)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Missatge de newsletter pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:newsletter_form_admin_notification.html.twig', array(
                'contact' => $contactMessage,
            )),
            $contactMessage->getEmail()
        );
    }

    /**
     * Send happy birthday notification to coworker.
     *
     * @param Coworker $coworker
     */
    public function sendCoworkerBirthdayNotification(Coworker $coworker)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $coworker->getEmail(),
            'Espai Kowo et desitja un Feliç Aniversari',
            $this->twig->render(':Mails:coworker_birthday_congratulation_notification.html.twig', array(
                'coworker' => $coworker,
            ))
        );
    }

    /**
     * Send happy birthday notification to Admin.
     *
     * @param Coworker $coworker
     */
    public function sendAdminBirthdayNotification(Coworker $coworker)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Demà és l\'aniversari de '.$coworker->getFullName(),
            $this->twig->render(':Mails:coworker_birthday_admin_notification.html.twig', array(
                'coworker' => $coworker,
            ))
        );
    }

    /**
     * Send a notification to fill coworker data form.
     *
     * @param Coworker $coworker
     */
    public function sendCoworkerDataFormNotification(Coworker $coworker)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $coworker->getEmail(),
            'Notificació pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:coworker_data_notification.html.twig', array(
                'coworker' => $coworker,
            ))
        );
    }

    /**
     * Send an admin notification once coworker data form is filled.
     *
     * @param Coworker $coworker
     */
    public function sendCoworkerDataFormAdminNotification(Coworker $coworker)
    {
        $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Notificació registre coworker '.$this->urlBase,
            $this->twig->render(':Mails:coworker_data_admin_notification.html.twig', array(
                'coworker' => $coworker,
            ))
        );
    }
}
