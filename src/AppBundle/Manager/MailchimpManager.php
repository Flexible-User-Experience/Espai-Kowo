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

    /** @var NotificationService*/
    private $messenger;

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
    public function __construct(MailChimp $mailChimp, NotificationService $messenger)
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
     * @return boolean       $result
     */
    public function subscribeContactToList(ContactMessage $contact, $listId)
    {
        $messenger = $this->messenger;
        $this->mailChimp->setListID($listId);
        $list = $this->mailChimp->getList();
        //Evaluate contact name
        $explodeName = explode(" ", $contact->getName());
        $countExplodeName = count($explodeName);
        if($countExplodeName === 1){
            $list->setMerge(array(
                'FNAME' => $explodeName[0]
                )
            );
        }else{
            $list->setMerge(array(
                'FNAME' => $explodeName[0],
                'LNAME' => $explodeName[1]
                )
            );
        }
        $list->setDoubleOptin(false);
        $result = $list->Subscribe($contact->getEmail());
        // Check contact to list
        if ($result == false) {
            $messenger->sendCommonAdminNotification('En ' . $contact->getEmail() . ' no s\'ha pogut registrar a la llista de Mailchimp');
        }
        // Send email notifications
        $messenger->sendCommonUserNotification($contact);
        $messenger->sendNewsletterSubscriptionAdminNotification($contact);

        return $result;
    }
}
