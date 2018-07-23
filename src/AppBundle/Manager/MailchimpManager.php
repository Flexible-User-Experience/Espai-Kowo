<?php

namespace AppBundle\Manager;

use AppBundle\Entity\ContactMessage;
use AppBundle\Service\NotificationService;
use \DrewM\MailChimp\MailChimp;

/**
 * Class MailchimpManager
 *
 * @category Manager
 */
class MailchimpManager
{
    const SUBSCRIBED = 'subscribed';

    /**
     * @var MailChimp $mailChimp
     */
     private $mailChimp;

    /**
     * @var NotificationService
     */
    private $messenger;

    /**
     * Methods
     */

    /**
     * MailchimpManager constructor.
     *
     * @param NotificationService $messenger
     * @param string $apiKey
     * @throws \Exception
     */
    public function __construct(NotificationService $messenger, $apiKey)
    {
        $this->mailChimp = new MailChimp($apiKey);
        $this->messenger = $messenger;
    }

    /**
     * Mailchimp Manager
     *
     * @param ContactMessage $contact
     * @param string $listId
     *
     * @return bool $result true if everything goes well or false otherwise
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function subscribeContactToList(ContactMessage $contact, $listId)
    {
        // evaluate contact name and subscribe
        $explodeName = explode(" ", $contact->getName());
        $mergeFields = array('FNAME' => $explodeName[0]);
        if (count($explodeName) >= 2) {
            $mergeFields['LNAME'] = $explodeName[1];
        }

        // check if user exists
        $result = $this->mailChimp->get('search-members', array(
            'list_id' => $listId,
            'query' => $contact->getEmail(),
        ));
        $isNew = 0 == intval($result['exact_matches']['total_items']);

        // make HTTP API request
        if ($isNew) {
            // new subscriber
            $result = $this->mailChimp->post('lists/' . $listId . '/members', array(
                'email_address' => $contact->getEmail(),
                'status'        => self::SUBSCRIBED,
                'merge_fields'  => $mergeFields,
            ));
        } else {
            // update record
            $result = $this->mailChimp->patch('lists/' . $listId . '/members/' . md5(strtolower($contact->getEmail())), array(
                'email_address' => $contact->getEmail(),
                'status'        => self::SUBSCRIBED,
                'merge_fields'  => $mergeFields,
            ));
        }

        // check error
        if (is_array($result) && $result['status'] != self::SUBSCRIBED ) {
            $this->messenger->sendCommonAdminNotification('En ' . $contact->getEmail() . ' no s\'ha pogut registrar a la llista de Mailchimp');

            return false;
        }

        return true;
    }
}
