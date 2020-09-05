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

namespace SwagPromotion\Components\Services;

use SwagPromotion\Struct\PromotedProduct;
use SwagPromotion\Struct\Promotion;

interface BasketServiceInterface
{
    /**
     * Inserts the given discount into the cart
     *
     * @param float  $discountGross
     * @param float  $discountNet
     * @param string $taxRate
     */
    public function insertDiscount(
        Promotion $promotion,
        $discountGross,
        $discountNet,
        $taxRate,
        array $matchingProducts
    );

    /**
     * @param PromotedProduct[] $promotedProducts
     */
    public function updatePromotedItems(Promotion $promotion, array $promotedProducts);

    /**
     * @param PromotedProduct[] $promotedProducts
     */
    public function insertDirectProductDiscount(Promotion $promotion, array $promotedProducts);
}
