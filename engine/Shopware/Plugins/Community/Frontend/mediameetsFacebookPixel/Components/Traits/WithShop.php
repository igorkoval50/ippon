<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Traits;

use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\Models\Shop\DetachedShop;
use Shopware\Models\Shop\Repository;
use Shopware\Models\Shop\Shop;

trait WithShop
{
    /**
     * @return ShopContextInterface
     */
    private function getShopContext()
    {
        return Shopware()
            ->Container()
            ->get('shopware_storefront.context_service')
            ->getShopContext();
    }

    /**
     * @return DetachedShop|Shop
     */
    private function getShop()
    {
        $container = Shopware()->Container();

        $shop = false;

        if ($container->initialized('shop')) {
            $shop = $container->get('shop');
        }

        if (! $shop) {
            $shop = $this->getActiveDefaultShop();
        }

        return $shop;
    }

    /**
     * @return DetachedShop
     */
    private function getActiveDefaultShop()
    {
        /** @var Repository $shopRepo */
        $shopRepo = Shopware()
            ->Container()
            ->get('models')
            ->getRepository(Shop::class);

        return $shopRepo->getActiveDefault();
    }
}
