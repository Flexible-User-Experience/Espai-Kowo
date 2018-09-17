<?php

namespace AppBundle\Service;

/**
 * Class CourierService
 *
 * @category Service
 */
class CourierService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Methods.
     */

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
     *
     * @return \Swift_Message
     */
    private function buildEmailMessage($from, $to, $subject, $body, $replyAddress = null)
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

        return $message;
    }

    /**
     * Send an email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string|null $replyAddress
     *
     * @return int messages delivered amount | 0 if failure
     */
    public function sendEmail($from, $to, $subject, $body, $replyAddress = null)
    {
        return $this->mailer->send($this->buildEmailMessage($from, $to, $subject, $body, $replyAddress));
    }

    /**
     * Send an email with an attachment
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string|null $replyAddress
     * @param string|null $attatchment
     *
     * @return int messages delivered amount | 0 if failure
     */
    public function sendEmailWithAttatchment($from, $to, $subject, $body, $replyAddress = null, $attatchment = null)
    {
        $message = $this->buildEmailMessage($from, $to, $subject, $body, $replyAddress);
        if (!is_null($attatchment)) {
            // TODO fetch attatchment
        }

        return $this->mailer->send($message);
    }
}
