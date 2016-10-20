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
     * Mailchimp Service
     *
     * @param ContactMessage $contact
     */
    public function subscribeContactToList(ContactMessage $contact)
    {
        $this->mailChimp->setListID('ad2298109f');
        $list = $this->mailChimp->getList();
        $list->setDoubleOptin(false);
        $list->Subscribe($contact->getEmail());
    }
}
