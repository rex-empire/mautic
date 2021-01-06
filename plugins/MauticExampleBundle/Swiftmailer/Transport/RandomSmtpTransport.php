<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExampleBundle\Swiftmailer\Transport;

use MauticPlugin\MauticExampleBundle\Randomizer\SmtpRandomizer;
use Monolog\Logger;

class RandomSmtpTransport extends \Swift_SmtpTransport
{
    /**
     * @var SmtpRandomizer
     */
    private $smtpRandomizer;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * RandomSmtpTransport constructor.
     *
     * @param null $security
     */
    public function __construct(SmtpRandomizer $smtpRandomizer, Logger $logger, $security = null)
    {
        $this->smtpRandomizer = $smtpRandomizer;
        $this->logger         = $logger;
        parent::__construct('localhost');
        $nothing = null;
        $this->setRandomSmtpServer($nothing, $this);
    }

    /**
     * @param null $failedRecipients
     *
     * @return int|void
     *
     * @throws \Exception
     */
    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $transport = (new \Swift_SmtpTransport('localhost'))->setStreamOptions(['ssl' => ['allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false]]);

        $this->setRandomSmtpServer($message, $transport);
        $transport->setStreamOptions(['ssl' => ['allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false]]);

        $mailer         = new \Swift_Mailer($transport);
        $mailbotAddress = $transport->getUsername();
        $message->getHeaders()->addTextHeader('Sender', $mailbotAddress);
        $message->getHeaders()->addTextHeader('X-Sender', $mailbotAddress);
        $message->getHeaders()->addTextHeader('Return-Path', $mailbotAddress);
        $mailer->send($message, $failedRecipients);
    }

    /**
     * Set random SMTP server.
     *
     * @param Swift_Mime_Message $message
     */
    private function setRandomSmtpServer(\Swift_Mime_SimpleMessage &$message = null, &$transport)
    {
        try {
            $this->smtpRandomizer->randomize($transport, $message);
            $this->logger->info(sprintf('Send by random SMTP server: %s with username %s and sender email %s to %s', $this->getHost(), $this->getUsername(), implode(',', $message ? array_keys($message->getFrom()) : []), $message ? implode(', ', array_keys($message->getTo())) : ''));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
