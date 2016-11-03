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
    /**
     * @var MailChimp $mailChimp
     */
     private $mailChimp;

    /**
     * @var NotificationService
     */
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
        $this->mailChimp = $mailChimp;
        $this->messenger = $messenger;
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
        $this->mailChimp->setListID($listId);
        $list = $this->mailChimp->getList();
        //Evaluate contact name
        $explodeName = explode(" ", $contact->getName());
        if(count($explodeName) === 1){
            $list->setMerge(array(
                'FNAME' => $explodeName[0]
                )
            );
        } else if (count($explodeName) >= 2) {
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
            $this->messenger->sendCommonAdminNotification('En ' . $contact->getEmail() . ' no s\'ha pogut registrar a la llista de Mailchimp');
        }

        return $result;
    }
}
