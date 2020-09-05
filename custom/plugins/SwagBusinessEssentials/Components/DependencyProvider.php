<?php

/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagBusinessEssentials\Components;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DependencyProvider
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the module with the given name, if any exists.
     *
     * @param string $moduleName
     *
     * @return mixed
     */
    public function getModule($moduleName)
    {
        /** @var \Shopware_Components_Modules $modules */
        $modules = $this->container->get('modules');

        if (!$modules->offsetExists($moduleName)) {
            return null;
        }

        return $modules->offsetGet($moduleName);
    }

    /**
     * Checks if a shop instance exists.
     *
     * @return bool
     */
    public function hasShop()
    {
        return $this->container->has('shop');
    }

    /**
     * Returns the currently active shop instance - if any given.
     *
     * @return \Shopware\Models\Shop\Shop|null
     */
    public function getShop()
    {
        if ($this->hasShop()) {
            return $this->container->get('shop');
        }

        return null;
    }

    /**
     * @return \Enlight_Components_Session_Namespace
     */
    public function getSession()
    {
        return $this->container->get('session');
    }
}
