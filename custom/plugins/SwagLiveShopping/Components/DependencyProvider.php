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

namespace SwagLiveShopping\Components;

use Shopware\Models\Shop\Repository as ShopRepository;
use Shopware\Models\Shop\Shop;
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
     */
    public function getModule($moduleName)
    {
        /** @var \Shopware_Components_Modules $modules */
        $modules = $this->container->get('modules');

        return $modules->getModule($moduleName);
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
     * Returns the currently active shop instance - if any given or active default.
     *
     * @return Shop
     */
    public function getShop()
    {
        if ($this->hasShop()) {
            return $this->container->get('shop');
        }

        /** @var ShopRepository $shopRepository */
        $shopRepository = $this->container->get('models')->getRepository(Shop::class);

        $defaultShop = $shopRepository->getActiveDefault();
        $this->container->set('shop', $defaultShop);

        return $defaultShop;
    }

    /**
     * @return \Enlight_Controller_Front
     */
    public function getFrontendController()
    {
        return $this->container->get('front');
    }

    /**
     * @return bool
     */
    public function hasSession()
    {
        if (!$this->hasShop()) {
            // Make sure container has a shop.
            $this->getShop();
        }

        return $this->container->has('session');
    }

    /**
     * @return \Enlight_Components_Session_Namespace
     */
    public function getSession()
    {
        return $this->container->get('session');
    }
}
