<?php

namespace AppBundle\Manager;

use AppBundle\Entity\ContactMessage;
use AppBundle\Service\NotificationService;
use MZ\MailChimpBundle\Services\MailChimp;

/**
 * Class MailchimpManager
 *
 * @category Manager
 * @package  AppBundle\Manager
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class MailchimpManager
{
    /** @var MailChimp $mailchimpService */
     private $mailChimp;

//    /** @var NotificationService*/
//    private $messenger;

    /**
     *
     *
     * Methods
     *
     *
     */

    /**
     * MailchimpManager constructor.
     *
     * @param MailChimp           $mailChimp
     * @param NotificationService $messenger
     */
    public function __construct($mailChimp, NotificationService $messenger)
    {
        $this->mailChimp    = $mailChimp;
        $this->messenger    = $messenger;
    }

    /**
     * Mailchimp Manager
     *
     * @param ContactMessage $contact
     * @param string         $listId
     *
     * @return boolean
     */
    public function subscribeContactToList(ContactMessage $contact, $listId)
    {
//        $messenger = $this->messenger;
        $this->mailChimp->setListID($listId);
        $list = $this->mailChimp->getList();
        $list->setMerge(array(
            'FNAME' => implode(explode(" ", $contact->getName(), -2)),
        //TODO    'LNAME' => implode(explode(" ", $nameSurname, -1)),
            )
        );
        $list->setDoubleOptin(false);
        $list->Subscribe($contact->getEmail());
        // Check contact to list
//        $result = $this->subscribeContactToList($contact, 'mailchimp_newsletter_list_id');
//        if ($result == false) {
//            $messenger->sendCommonAdminNotification('En ' . $contact->getEmail() . ' no s\'ha pogut registrar a la llista de Mailchimp');
//        }
        // Send email notifications
//        $messenger->sendCommonUserNotification($contact);
//        $messenger->sendNewsletterSubscriptionAdminNotification($contact);

        return $list->Subscribe();
    }
}
