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
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;
use SwagPromotion\Components\Promotion\DiscountCommand\Command\DiscountCommand;
use SwagPromotion\Components\Promotion\DiscountHandler\DiscountHandler;
use SwagPromotion\Models\Promotion as PromotionEntity;
use SwagPromotion\Struct\PromotedProduct;
use SwagPromotion\Struct\Promotion;

/**
 * PercentageProductHandler handles percentage discounts on products
 */
class PercentageProductHandler implements DiscountHandler
{
    const PERCENTAGE_PRODUCT_HANDLER_NAME = 'product.percentage';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    public function __construct(Session $session, ContextServiceInterface $contextService)
    {
        $this->session = $session;
        $this->contextService = $contextService;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountCommand($basket, $stackedProducts, Promotion $promotion)
    {
        $discount = 0.0;
        $promotedProducts = $this->buildPromotedProductStructs($stackedProducts);

        $promotedItems = [];

        if ($this->session->offsetExists('swag-promotion-direct-promoted-items')) {
            $promotedItems = $this->session->offsetGet('swag-promotion-direct-promoted-items');
        }

        /** @var ShopContext $context */
        $context = $this->contextService->getShopContext();
        $factor = $context->getCurrency()->getFactor();

        foreach ($stackedProducts as $stack) {
            $stackDiscount = $stack[0]['price'] * ($promotion->amount / 100) / $factor;
            $discount += $stackDiscount;
            if (isset($promotedProducts[$stack[0]['basketItemId']])) {
                $promotedProducts[$stack[0]['basketItemId']]->increaseDiscount($stackDiscount);
                if ($promotion->discountDisplay === PromotionEntity::DISCOUNT_DISPLAY_DIRECT) {
                    if (!isset($promotedItems[$stack[0]['basketItemId']])) {
                        $promotedItems[$stack[0]['basketItemId']] = [
                            'appliedPromotions' => [],
                            'discount' => 0,
                        ];
                    }
                    if (!in_array($promotion->id, $promotedItems[$stack[0]['basketItemId']]['appliedPromotions'])) {
                        $promotedItems[$stack[0]['basketItemId']]['discount'] += $stackDiscount;
                        $promotedItems[$stack[0]['basketItemId']]['appliedPromotions'][] = $promotion->id;
                        $promotedProducts[$stack[0]['basketItemId']]->increaseDirectDiscount($stackDiscount);
                    }
                }
            }
        }

        $this->session->offsetSet('swag-promotion-direct-promoted-items', $promotedItems);

        return new DiscountCommand($discount, array_values($promotedProducts));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name === self::PERCENTAGE_PRODUCT_HANDLER_NAME;
    }

    /**
     * @return PromotedProduct[]
     */
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
}
