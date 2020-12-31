<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExampleBundle\Randomizer;

use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticExampleBundle\Exception\HostNotExistinCsvRowExpection;
use MauticPlugin\MauticExampleBundle\Exception\IntegrationDisableException;
use MauticPlugin\MauticExampleBundle\Exception\SmtpCsvListNotExistException;
use MauticPlugin\MauticExampleBundle\Swiftmailer\Transport\RandomSmtpTransport;

class SmtpRandomizer
{
    /** @var array */
    private $config;

    /** @var array */
    private $smtps;

    /** @var array */
    private $smtp;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $integration = $integrationHelper->getIntegrationObject('Example');
        error_log('************** IN THE!!!!!! CUT ****** ');
//        var_dump($integration);
//        var_dump($integration->getIntegrationSettings());
//        var_dump($integration->getIntegrationSettings()->getIsPublished());

        if (!$integration || true !== $integration->getIntegrationSettings()->getIsPublished()) {
            throw new IntegrationDisableException('Integration RandomSmtp doesn\'t exist or is unpublished');
        }

        $config = $this->config = $integration->mergeConfigToFeatureSettings();
        $smtps  = explode("\n", $config['smtps']);
        $smtp   = end($smtps);
        if (empty($smtp)) {
            throw new SmtpCsvListNotExistException('Smtp CSV list not exist. Please setup it in plugin setting.');
        }

        $this->smtps = array_map('str_getcsv', $smtps);
    }

    /**
     * @param RandomSmtpTransport       $randomSmtpTransport
     * @param \Swift_Mime_SimpleMessage $message
     *
     * @throws HostNotExistinCsvRowExpection
     */
    public function randomize(\Swift_SmtpTransport &$randomSmtpTransport, \Swift_Mime_SimpleMessage &$message = null)
    {
        $smtp = $this->getRandomSmtp();
        if (!$host = ArrayHelper::getValue(0, $smtp)) {
            throw new HostNotExistinCsvRowExpection('Can\'t find host on column possition '.sprintf('"%s"', 0));
        }

        //alpha.rexvideo.io
        $randomSmtpTransport->setHost($host);
        // 587
        $randomSmtpTransport->setPort(ArrayHelper::getValue(1, $smtp, 25));
        // tls
        $randomSmtpTransport->setEncryption(ArrayHelper::getValue(2, $smtp, ''));
        // plain
        $randomSmtpTransport->setAuthMode(ArrayHelper::getValue(3, $smtp, ''));
        // hi@alpha.rexvideo.io
        $randomSmtpTransport->setUsername(ArrayHelper::getValue(4, $smtp, ''));
        // fuckyeah
        $randomSmtpTransport->setPassword(ArrayHelper::getValue(5, $smtp, ''));

        // change sender
        if ($message && $fromEmail = ArrayHelper::getValue($this->getConfigParamter('fromEmail'), $smtp, false)) {
            $message->setFrom($fromEmail, ArrayHelper::getValue($this->getConfigParamter('fromName'), $smtp, null));
            $this->smtp = null;
        }
    }

    /**
     * @return array
     */
    private function getRandomSmtp()
    {
        if (!$this->smtp) {
            $smtps = $this->smtps;
            shuffle($smtps);
            $this->smtp  = end($smtps);
        }

        return $this->smtp;
    }

    /**
     * @param $key
     *
     * @return int
     */
    private function getConfigParamter($key)
    {
        if (isset($this->config[$key]) && '' !== $this->config[$key]) {
            return (int) $this->config[$key];
        }
    }
}
