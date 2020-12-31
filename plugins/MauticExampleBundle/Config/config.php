<?php

declare(strict_types=1);

return [
    'name'        => 'Example Bundle!',
    'description' => 'Desc :/',
    'version'     => '0.0.0',
    'author'      => 'Mautic Community',

    'services' => [
        'commands' => [
            'cronfig.command.do_nothing' => [
                'class' => \MauticPlugin\MauticExampleBundle\Command\DoNothing::class,
                'tag'   => 'console.command',
            ],
        ],
        'other'   => [
            'mautic.transport.random' => [
                'class'        => \MauticPlugin\MauticExampleBundle\Swiftmailer\Transport\RandomSmtpTransport::class,
                'arguments'    => [
                    'mautic.random.smtp.randomizer',
                    'monolog.logger.mautic',
                ],
                'tag'          => 'mautic.email_transport',
                'tagArguments' => [
                    \Mautic\EmailBundle\Model\TransportType::TRANSPORT_ALIAS => 'Moneymaker',
                ],
            ],
            'mautic.random.smtp.randomizer' => [
                'class'        => MauticPlugin\MauticExampleBundle\Randomizer\SmtpRandomizer::class,
                'arguments'    => [
                    'mautic.helper.integration',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.example' => [
                'class'     => \MauticPlugin\MauticExampleBundle\Integration\ExampleIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
    ],
];
