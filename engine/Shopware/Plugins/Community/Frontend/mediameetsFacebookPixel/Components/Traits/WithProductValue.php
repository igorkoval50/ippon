<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Traits;

use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\mediameetsFacebookPixel\Components\Calculators\PriceValueCalculator;

trait WithProductValue
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
     * @param int|float $price
     * @param int|float $tax
     * @return float
     */
    private function getProductValue($price, $tax)
    {
        $customerGroup = $this->getShopContext()
            ->getCurrentCustomerGroup();

        $priceCalculator = new PriceValueCalculator($customerGroup);

        return $priceCalculator->getPrice(
            $price,
            $tax
        );
    }
}
