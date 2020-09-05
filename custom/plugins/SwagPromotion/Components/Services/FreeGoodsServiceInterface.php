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

use SwagPromotion\Struct\Promotion;

interface FreeGoodsServiceInterface
{
    /**
     * adds the given product as free good to the basket
     * also updates the basket attribute field
     *
     * @param string $orderNumber
     * @param int    $promotionId
     * @param int    $quantity
     */
    public function addArticleAsFreeGood($orderNumber, $promotionId, $quantity = 1);

    /**
     * if the quantity of a free good products is lower than the amount of promotion IDs, we have to remove some IDs
     *
     * @param int $basketId
     * @param int $quantity
     *
     * @return bool
     */
    public function updateFreeGoodsItem($basketId, $quantity);

    /**
     * if a free good promotion is not valid anymore, update data in the basket attribute field,
     * so it does not affect other calculations
     *
     * @param array|null $basketItems
     * @param int        $promotionId
     */
    public function clearFreeGoodsFromBasket($basketItems, array $freeGoods, $promotionId);

    /**
     * @return bool
     */
    public function isAchievedStack(Promotion $promotion, array $matches);

    /**
     * @return array
     */
    public function applyInfo(array $promotionIds, array $list);

    public function checkLineItem(array $lineItem, int $promotionId, array $freeGoodProductIds): bool;
}
