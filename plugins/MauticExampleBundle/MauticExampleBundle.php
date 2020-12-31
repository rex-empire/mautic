<?php

namespace MauticPlugin\MauticExampleBundle;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Mautic\PluginBundle\Entity\Plugin;

class MauticExampleBundle extends PluginBundleBase
{
    public function boot()
    {
        error_log('FUCK YOU boot');
//        var_dump($this);
        error_log('FUCK YOU boot 2');

        parent::boot();
    }

    public static function onPluginInstall(Plugin $plugin, MauticFactory $factory, $metadata = null, $installedSchema = null)
    {
        error_log('GET SUCKED');
        error_log($plugin);
        error_log($installedSchema);
        error_log($metadata);

        if (null !== $metadata) {
//            self::installPluginSchema($metadata, $factory);
        }

        // Do other install stuff
    }
}
