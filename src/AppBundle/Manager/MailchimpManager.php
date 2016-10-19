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
    public function mailchimpSubscribe(ContactMessage $contact)
    {
//        $contact = new ContactMessage();
        $mailChimp = $this->mailChimp;

        $mailChimp->setListID('mailchimp_newsletter_list_id');
        $list = $mailChimp->getList();
        $list->setDoubleOptin(false);
        $list->Subscribe($contact->getEmail());
    }
}
