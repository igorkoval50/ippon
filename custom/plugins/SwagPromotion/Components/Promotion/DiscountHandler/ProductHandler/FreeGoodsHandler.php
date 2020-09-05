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

namespace SwagPromotion\Components\Promotion\DiscountHandler\ProductHandler;

use SwagPromotion\Components\Promotion\DiscountCommand\Command\FreeGoodsCommand;
use SwagPromotion\Components\Promotion\DiscountHandler\DiscountHandler;
use SwagPromotion\Components\Services\FreeGoodsServiceInterface;
use SwagPromotion\Struct\Promotion;

class FreeGoodsHandler implements DiscountHandler
{
    const FREE_GOODS_HANDLER_NAME = 'product.freegoods';

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
        $freeGoodArticleIds = $promotion->freeGoods;

        $discount = 0.0;

        // There are product / steps defined for the FreeGoods - and the customer did not met the requirement
        if (empty($stackedProducts) || empty($freeGoodArticleIds)) {
            return new FreeGoodsCommand($discount);
        }

        // Get price of existing FreeGood
        foreach ($basket['content'] as $lineItem) {
            $isFreeGoodItem = $this->freeGoodsService->checkLineItem($lineItem, (int) $promotion->id, $freeGoodArticleIds);
            if (!$isFreeGoodItem) {
                continue;
            }

            if (!empty($lineItem['priceNumeric'])) {
                $discount = (float) $lineItem['priceNumeric'];
                break;
            }
        }

        return new FreeGoodsCommand($discount);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name === self::FREE_GOODS_HANDLER_NAME;
    }
}
