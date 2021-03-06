<?php

namespace AppBundle\Service;

use AppBundle\Entity\ContactMessage;
use AppBundle\Entity\Coworker;
use AppBundle\Entity\Invoice;

/**
 * Class NotificationService.
 *
 * @category Service
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
     * @var string mailer destination parameter
     */
    private $amd;

    /**
     * @var string mailer URL base parameter
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
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCommonUserNotification(ContactMessage $contactMessage)
    {
        return $this->messenger->sendEmail(
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
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCommonAdminNotification($text)
    {
        return $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Notificació pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:common_admin_notification.html.twig', array(
                'text' => $text,
            ))
        );
    }

    /**
     * Send a common notification mail to admin user from contact context.
     *
     * @param ContactMessage $contactMessage
     * @param string $fromActionText
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCommonContactAdminNotification(ContactMessage $contactMessage, $fromActionText = 'homepage')
    {
        return $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Missatge de '.$fromActionText.' pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:newsletter_form_contact_admin_notification.html.twig', array(
                'contact' => $contactMessage,
                'fromActionText' => $fromActionText
            )),
            $contactMessage->getEmail()
        );
    }

    /**
     * Send free trial notification to admin user.
     *
     * @param ContactMessage $contactMessage
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendFreeTrialAdminNotification(ContactMessage $contactMessage)
    {
        return $this->messenger->sendEmail(
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
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendContactAdminNotification(ContactMessage $contactMessage)
    {
        return $this->messenger->sendEmail(
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
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendUserBackendAnswerNotification(ContactMessage $contactMessage)
    {
        return $this->messenger->sendEmail(
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
     * @param string $fromActionText
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendNewsletterSubscriptionAdminNotification(ContactMessage $contactMessage, $fromActionText = 'newsletter')
    {
        return $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Missatge de '.$fromActionText.' pàgina web '.$this->urlBase,
            $this->twig->render(':Mails:newsletter_form_admin_notification.html.twig', array(
                'contact' => $contactMessage,
                'fromActionText' => $fromActionText
            )),
            $contactMessage->getEmail()
        );
    }

    /**
     * Send happy birthday notification to coworker.
     *
     * @param Coworker $coworker
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCoworkerBirthdayNotification(Coworker $coworker)
    {
        return $this->messenger->sendEmail(
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
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendAdminBirthdayNotification(Coworker $coworker)
    {
        return $this->messenger->sendEmail(
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
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCoworkerDataFormNotification(Coworker $coworker)
    {
        return $this->messenger->sendEmail(
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
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendCoworkerDataFormAdminNotification(Coworker $coworker)
    {
        return $this->messenger->sendEmail(
            $this->amd,
            $this->amd,
            'Notificació registre coworker '.$this->urlBase,
            $this->twig->render(':Mails:coworker_data_admin_notification.html.twig', array(
                'coworker' => $coworker,
            ))
        );
    }

    /**
     * Send invoice notification with invoice PDF attachment
     *
     * @param Invoice $invoice
     * @param \TCPDF $pdf
     *
     * @return int messages delivered amount | 0 if failure
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendInvoicePdfNotification(Invoice $invoice, \TCPDF $pdf)
    {

        return $this->messenger->sendEmailWithPdfAttached(
            $this->amd,
            $invoice->getCustomer()->getEmail(),
            $invoice->getCustomer()->getName(),
            'Factura '.$invoice->getInvoiceNumberWithF(),
            $this->twig->render(':Mails:customer_invoice_notification.html.twig', array(
                'invoice' => $invoice,
            )),
            'factura_'.$invoice->getUnderscoredInvoiceNumber().'.pdf',
            $pdf
        );
    }
}
