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

namespace SwagNewsletter\Components;

use Shopware\Models\Shop\Repository as ShopRepository;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DependencyProvider implements DependencyProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getModule($moduleName)
    {
        return $this->container->get('modules')->getModule($moduleName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasShop()
    {
        return $this->container->has('shop');
    }

    /**
     * {@inheritdoc}
     */
    public function getShop()
    {
        if ($this->hasShop()) {
            return $this->container->get('shop');
        }

        /** @var ShopRepository $shopRepository */
        $shopRepository = $this->container->get('models')->getRepository(Shop::class);

        return $shopRepository->getActiveDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendController()
    {
        return $this->container->get('front');
    }

    /**
     * {@inheritdoc}
     */
    public function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($parameter)
    {
        return $this->container->hasParameter($parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }
}
