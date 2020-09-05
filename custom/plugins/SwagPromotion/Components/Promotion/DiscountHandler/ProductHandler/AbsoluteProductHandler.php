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

use Enlight_Components_Session_Namespace as Session;
use SwagPromotion\Components\Promotion\CurrencyConverter;
use SwagPromotion\Components\Promotion\DiscountCommand\Command\DiscountCommand;
use SwagPromotion\Components\Promotion\DiscountHandler\DiscountHandler;
use SwagPromotion\Models\Promotion as PromotionEntity;
use SwagPromotion\Struct\PromotedProduct;
use SwagPromotion\Struct\Promotion;

/**
 * AbsoluteProductHandler handles absolute discounts on products.
 * Discounts will never be higher then the actual product's price.
 */
class AbsoluteProductHandler implements DiscountHandler
{
    const ABSOLUTE_PRODUCT_HANDLER_NAME = 'product.absolute';

    /**
     * @var CurrencyConverter
     */
    private $currencyConverter;

    /**
     * @var Session
     */
    private $session;

    public function __construct(CurrencyConverter $currencyHandler, Session $session)
    {
        $this->currencyConverter = $currencyHandler;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountCommand($basket, $stackedProducts, Promotion $promotion)
    {
        $amount = $this->currencyConverter->convert($promotion->amount);
        $discount = 0.0;
        $promotedProducts = $this->buildPromotedProductStructs($stackedProducts, $amount);

        // calculate the total discount
        // if a stack has a total less then the amount,
        // the granted discount amount will be $price - 0.01 ct
        // so that the product / stack costs 0.01 ct
        foreach ($stackedProducts as $stack) {
            $prices = (float) array_sum(array_column($stack, 'price'));
            if ($prices > $amount) {
                $discount += $amount;
                $this->increaseDiscountByAmount($promotion, $stack, $amount, $promotedProducts);
            } elseif ($prices === $amount) {
                $discount += $prices - 0.01;
                $this->increaseDiscountByPrice($promotion, $stack, $promotedProducts);
            }
        }

        return new DiscountCommand($discount, array_values($promotedProducts));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name === self::ABSOLUTE_PRODUCT_HANDLER_NAME;
    }

    private function buildPromotedProductStructs(array $stackedProducts)
    {
        $structs = [];
        foreach ($stackedProducts as $stack) {
            foreach ($stack as $stackedProduct) {
                if (!isset($structs[$stackedProduct['basketItemId']])) {
                    $structs[$stackedProduct['basketItemId']] = new PromotedProduct($stackedProduct);
                }
            }
        }

        return $structs;
    }

    /**
     * @param float             $amount
     * @param PromotedProduct[] $promotedProducts
     */
    private function increaseDiscountByAmount(Promotion $promotion, array $stack, $amount, array $promotedProducts)
    {
        $amount /= $this->currencyConverter->getFactor();
        $itemIds = array_column($stack, 'basketItemId');
        $promotedItems = [];

        if ($this->session->offsetExists('swag-promotion-direct-promoted-items')) {
            $promotedItems = $this->session->offsetGet('swag-promotion-direct-promoted-items');
        }

        foreach ($itemIds as $itemId) {
            if (isset($promotedProducts[$itemId])) {
                $promotedProducts[$itemId]->increaseDiscount($amount);
                if ($promotion->discountDisplay === PromotionEntity::DISCOUNT_DISPLAY_DIRECT) {
                    if (!isset($promotedItems[$itemId])) {
                        $promotedItems[$itemId] = [
                            'appliedPromotions' => [],
                            'discount' => 0,
                        ];
                    }
                    if (!in_array($promotion->id, $promotedItems[$itemId]['appliedPromotions'])) {
                        $promotedItems[$itemId]['discount'] += $amount;
                        $promotedItems[$itemId]['appliedPromotions'][] = $promotion->id;
                        $promotedProducts[$stack[0]['basketItemId']]->increaseDirectDiscount($amount);
                    }
                }
            }
        }

        $this->session->offsetSet('swag-promotion-direct-promoted-items', $promotedItems);
    }

    /**
     * @param array             $stack
     * @param PromotedProduct[] $promotedProducts
     */
    private function increaseDiscountByPrice(Promotion $promotion, $stack, array $promotedProducts)
    {
        $itemIds = array_column($stack, 'basketItemId');
        $lastItemIdInStack = array_pop($itemIds);

        $promotedItems = [];

        if ($this->session->offsetExists('swag-promotion-direct-promoted-items')) {
            $promotedItems = $this->session->offsetGet('swag-promotion-direct-promoted-items');
        }

        foreach ($itemIds as $itemId) {
            if (isset($promotedProducts[$itemId])) {
                $promotedProduct = $promotedProducts[$itemId];
                $price = $promotedProduct->getPrice();

                if ($lastItemIdInStack === $itemId) {
                    $price = $price - 0.01;
                }

                $promotedProduct->increaseDiscount($price);
                if ($promotion->discountDisplay === PromotionEntity::DISCOUNT_DISPLAY_DIRECT) {
                    if (!isset($promotedItems[$itemId])) {
                        $promotedItems[$itemId] = [
                            'appliedPromotions' => [],
                            'discount' => 0,
                        ];
                    }
                    $promotedItems[$itemId]['discount'] += $price;
                    $promotedItems[$itemId]['appliedPromotions'][] = $promotion->id;
                    $promotedProducts[$stack[0]['basketItemId']]->increaseDirectDiscount($price);
                }
            }
        }
    }
}
