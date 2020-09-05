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

namespace SwagCustomProducts\Components\Services;

use Shopware\Bundle\StoreFrontBundle\Struct\Product;

/**
 * This interface is for easy extending or overwriting the BasketManager
 */
interface BasketManagerInterface
{
    // CustomProduct modes as constants
    const MODE_PRODUCT = 1;
    const MODE_OPTION = 2;
    const MODE_VALUE = 3;

    /**
     * Adds a customProduct to the basket.
     *
     * returns the id of the row
     *
     * @param Product $product
     * @param string  $hash
     * @param int     $quantity
     *
     * @return string
     */
    public function addToBasket($product, $hash, $quantity);

    /**
     * Updates the quantity of a CustomProduct by the CustomProductHash
     *
     * @param string $hash
     * @param int    $quantity
     */
    public function setQuantity($hash, $quantity);

    /**
     * Returns all CustomProducts in the basket
     *
     * @return array
     */
    public function readBasket();

    /**
     * returns a single basket position by id
     *
     * @param int $basketId
     *
     * @return array
     */
    public function readBasketPosition($basketId);

    /**
     * Deletes a CustomProduct from the basket by the given CustomProductHash
     *
     * @param string $hash
     */
    public function deleteFromBasket($hash);

    /**
     * update the taxRate.
     */
    public function updateBasketTaxes();
}
