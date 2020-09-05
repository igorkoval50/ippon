<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace KibVariantListing\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin\ConfigReader;
use Shopware\Components\Theme\LessDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Resources implements SubscriberInterface
{
    private $pluginPath;
    /**
     * @var ConfigReader
     */
    private $config;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ConfigReader $config
     * @param ContainerInterface $container
     * @param string $pluginPath
     */
    public function __construct(
        ConfigReader $config,
        ContainerInterface $container,
        $pluginPath
    )
    {
        $this->pluginPath = $pluginPath;
        $this->config = $config;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'Theme_Compiler_Collect_Plugin_Less' => 'onAddLessFiles',
            'Theme_Compiler_Collect_Plugin_Javascript' => ['onAddJavascriptFiles', 100]
        );
    }

    /**
     * Provides the file collection for javascript
     *
     * @return ArrayCollection
     */
    public function onAddJavascriptFiles(\Enlight_Event_EventArgs $args)
    {
        $jsDir = $this->pluginPath . '/Resources/views/kib/frontend/_public/src/js/';

        return new ArrayCollection(array(
            $jsDir . 'jquery.variants_in_listing.js'
        ));
    }

    /**
     * Provides the file collection for less
     *
     * @return ArrayCollection
     */
    public function onAddLessFiles(\Enlight_Event_EventArgs $args)
    {
        $config = $this->config->getByPluginName($this->getPlugin()->getName(), $args->get('shop'));

        $less = new LessDefinition(
            array(
                'kib-zindex' => $config['zindex'],
                'kib-slideVariants' => $config['slideVariants']
            ),
            array(
                $this->pluginPath . '/Resources/views/kib/frontend/_public/src/less/all.less'
            ),
            $this->pluginPath
        );

        return new ArrayCollection(array($less));
    }

    /**
     * @return \KibVariantListing\KibVariantListing
     */
    private function getPlugin()
    {
        return $this->container->get('kernel')->getPlugins()['KibVariantListing'];
    }
}
