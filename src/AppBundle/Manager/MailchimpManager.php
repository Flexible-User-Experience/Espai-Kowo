<?php

namespace AppBundle\Manager;

use AppBundle\Entity\ContactMessage;
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
     * @param MailChimp $mailChimp
     */
    public function __construct($mailChimp)
    {
        $this->mailChimp = $mailChimp;
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
        $this->mailChimp->setListID($listId);
        $list = $this->mailChimp->getList();
        $list->setMerge(array(
            'FNAME' => implode(explode(" ", $contact->getName(), -2)),
        //TODO    'LNAME' => implode(explode(" ", $nameSurname, -1)),
            )
        );
        $list->setDoubleOptin(false);
        $list->Subscribe($contact->getEmail());

        return $list->Subscribe();
    }
}
