<?php

namespace AppBundle\Service;
use AppBundle\Entity\ContactMessage;

/**
 * Class CourierService
 *
 * @category Service
 * @package  AppBundle\Service
 * @author   David Romaní <david@flux.cat>
 */
class CourierService
{
    /** @var \Swift_Mailer */
    private $mailer;

    /**
     * CourierService constructor
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send an email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string|null $replyAddress
     */
    public function sendEmail($from, $to, $subject, $body, $replyAddress = null)
    {
        $message = new \Swift_Message();
        $message
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body)
            ->setCharset('UTF-8')
            ->setContentType('text/html');
        if (!is_null($replyAddress)) {
            $message->setReplyTo($replyAddress);
        }
        //TODO try catch exception with return
        $this->mailer->send($message);
    }
}
