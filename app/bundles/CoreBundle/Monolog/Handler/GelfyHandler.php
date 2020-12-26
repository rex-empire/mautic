<?php

/*
 * @copyright   2020 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// THIS IS FOR EXPORTING
namespace Mautic\CoreBundle\Monolog\Handler;

// THIS IS FOR IMPORTING
use Monolog\Handler\GelfHandler;
//use Gelf\PublisherInterface;



class GelfyHandler extends GelfHandler
{
    public function __construct($a, $b)
    {
//        $logPath     = $coreParametersHelper->get('log_path');
//        $logFileName = $coreParametersHelper->get('log_file_name');
//        $maxFiles    = $coreParametersHelper->get('max_log_files');
//        $debugMode   = $coreParametersHelper->get('debug', false) || (defined('MAUTIC_ENV') && 'dev' === MAUTIC_ENV);
//        $debugMode = true;
//        $level       = $debugMode ? Logger::DEBUG : Logger::NOTICE;

//        if ($debugMode) {
//            $this->setFormatter($exceptionFormatter);
//        }

        // udp://172.16.0.38:12201

//         $pub = new \Gelf\Publisher(new \Gelf\Transport\UdpTransport("172.16.0.38", 12201));
         $pub = new \Gelf\Publisher(new \Gelf\Transport\UdpTransport("192.198.116.210", 12201));


//        $transport = new Gelf\Transport\UdpTransport("127.0.0.1", 12201, Gelf\Transport\UdpTransport::CHUNK_SIZE_LAN);


        // To mute all connection related exceptions, as it may be useful in logging, we can wrap the transport:
        //
        // $transport = new Gelf\Transport\IgnoreErrorTransportWrapper($transport);

        // While the UDP transport is itself a publisher, we wrap it in a real Publisher for convenience.
        // A publisher allows for message validation before transmission, and also supports sending
        // messages to multiple backends at once.
        //        $publisher = new Gelf\Publisher();
        //        $publisher->addTransport($transport);

        // $logger = new Gelf\Logger();

        error_log('WTF IS THIS');
        // $logger->alert("Foobaz!");

//        parent::__construct(sprintf('%s/%s', $logPath, $logFileName), $maxFiles, $level);
        parent::__construct($pub);
    }
}
