<?php

/*
 * @copyright  2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExampleBundle\Integration;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExampleIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'Example';
    }

    /**
     * Return's authentication method such as oauth2, oauth1a, key, etc.
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        error_log('****** COME ONNNN 3 *******');

        return 'none';
    }

    /**
     * Return array of key => label elements that will be converted to inputs to
     * obtain from the user.
     *
     * @return array
     */
    public function getRequiredKeyFields()
    {
        error_log('****** COME ONNNN 2 *******');

        return [
            'username' => 'FINDME',
        ];
    }

    /**
     * @param FormBuilder|Form $builder
     * @param array            $data
     * @param string           $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        error_log('****** COME ONNNN *******');
        if ('features' === $formArea) {
            $builder->add(
                'auto_update',
                YesNoButtonGroupType::class,
                [
                    'label' => 'Crush it?',
                    'data'  => (isset($data['auto_update'])) ? (bool) $data['auto_update'] : false,
                    'attr'  => [
                        'tooltip' => 'mautic.plugin.clearbit.auto_update.tooltip',
                    ],
                ]
            );
            $builder->add(
                'smtps',
                TextareaType::class,
                [
                    'label'      => 'Servers',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'attr'       => [
                        'class'          => 'form-control',
                        'tooltip'        => 'mautic.random.smtp.smtps.tooltip',
                        'rows'           => 10,
                    ],
                ]
            );
        }
    }

    public function shouldAutoUpdate()
    {
        $featureSettings = $this->getKeys();

        return (isset($featureSettings['auto_update'])) ? (bool) $featureSettings['auto_update'] : false;
    }

    /**
     * {@inheritdoc}
     *
     * @param $section
     *
     * @return string|array
     */
    public function getFormNotes($section)
    {
        if ('custom' === $section) {
            return [
                'template'   => 'MauticClearbitBundle:Integration:form.html.php',
                'parameters' => [
                    'mauticUrl' => $this->router->generate(
                        'mautic_plugin_clearbit_index', [], UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ],
            ];
        }

        return parent::getFormNotes($section);
    }
}
