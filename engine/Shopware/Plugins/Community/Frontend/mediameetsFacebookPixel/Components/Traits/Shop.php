<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Traits;

use Shopware\Models\Shop\DetachedShop;
use Shopware\Models\Shop\Repository;

trait Shop
{
    /**
     * @return DetachedShop|\Shopware\Models\Shop\Shop
     */
    private function getShop()
    {
        $shop = false;

        $container = Shopware()->Container();

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
        $container = Shopware()->Container();

        /** @var Repository $shopRepo */
        $shopRepo = $container
            ->get('models')
            ->getRepository('Shopware\Models\Shop\Shop');

        return $shopRepo->getActiveDefault();
    }
}
