<?php declare(strict_types=1);
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

namespace SwagPromotion\Components\Promotion\DiscountHandler\ProductHandler;

use SwagPromotion\Components\Promotion\DiscountCommand\Command\FreeGoodsBundleCommand;
use SwagPromotion\Components\Promotion\DiscountHandler\DiscountHandler;
use SwagPromotion\Components\Services\FreeGoodsServiceInterface;
use SwagPromotion\Struct\Promotion;

class FreeGoodsBundleHandler implements DiscountHandler
{
    const FREE_GOODS_BUNDLE_HANDLER_NAME = 'product.freegoodsbundle';

    /**
     * @var FreeGoodsServiceInterface
     */
    private $freeGoodsService;

    public function __construct(FreeGoodsServiceInterface $freeGoodsService)
    {
        $this->freeGoodsService = $freeGoodsService;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountCommand($basket, $stackedProducts, Promotion $promotion)
    {
        $freeGoodProductIds = $promotion->freeGoods;
        $discount = 0.0;
        $freeGoodsAmount = 0;

        if (empty($freeGoodProductIds)) {
            return new FreeGoodsBundleCommand($discount, $freeGoodsAmount);
        }

        foreach ($basket['content'] as $lineItem) {
            $isFreeGoodItem = $this->freeGoodsService->checkLineItem($lineItem, (int) $promotion->id, $freeGoodProductIds);

            if (!$isFreeGoodItem) {
                continue;
            }

            if (!empty($lineItem['priceNumeric'])) {
                $discount += (float) $lineItem['priceNumeric'] * $lineItem['quantity'];
                $freeGoodsAmount += $lineItem['quantity'];
                $promotion->freeGoodBundleMaxQuantityCurrentSelection += $lineItem['quantity'];
            }
        }

        if ($freeGoodsAmount > count($stackedProducts)) {
            $this->freeGoodsService->clearFreeGoodsFromBasket($basket['content'], $promotion->freeGoods, $promotion->id);

            return new FreeGoodsBundleCommand(0.0, 0);
        }

        return new FreeGoodsBundleCommand($discount, $freeGoodsAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name === self::FREE_GOODS_BUNDLE_HANDLER_NAME;
    }
}
