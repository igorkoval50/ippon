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

namespace SwagPromotion\Components\Promotion\DiscountCommand\Handler;

use SwagPromotion\Components\Promotion\DiscountCommand\Command\Command;
use SwagPromotion\Components\Promotion\DiscountCommand\Command\FreeGoodsBundleCommand;
use SwagPromotion\Components\Promotion\Tax;
use SwagPromotion\Components\Services\BasketServiceInterface;
use SwagPromotion\Struct\Promotion;

class FreeGoodsBundleCommandHandler implements CommandHandler
{
    /**
     * @var Tax
     */
    private $taxCalculator;

    /**
     * @var BasketServiceInterface
     */
    private $basketService;

    public function __construct(Tax $taxCalculator, BasketServiceInterface $basketService)
    {
        $this->taxCalculator = $taxCalculator;
        $this->basketService = $basketService;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Command $command, Promotion $promotion, array $basket, array $matchingProducts)
    {
        /** @var FreeGoodsBundleCommand $command */
        $amount = $command->getDiscountAmount();

        // Do not insert discounts with amount 0
        if ($amount === 0.0) {
            return false;
        }

        $freeGoods = $this->getFreeGoods($promotion, $basket);

        $result = $this->taxCalculator->calculate(-$amount, $basket, $freeGoods);

        $this->basketService->insertDiscount($promotion, -$amount, $result['net'], $result['taxRate'], $matchingProducts);

        $matchCount = $this->getMatchingProductsCount($matchingProducts);

        return $command->getFreeGoodsAmount() >= $matchCount;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name === FreeGoodsBundleCommand::FREE_GOODS_BUNDLE_COMMAND_NAME;
    }

    private function getFreeGoods(Promotion $promotion, array $basket): array
    {
        $freeGoods = [];
        foreach ($basket['content'] as $product) {
            $data = unserialize($product['isFreeGoodByPromotionId'] ?: '{}');

            if (!$data) {
                continue;
            }

            if (in_array($promotion->id, $data, false)) {
                $freeGoods[] = $product;
            }
        }

        return $freeGoods;
    }

    private function getMatchingProductsCount(array $matchingProducts)
    {
        $amount = 0;
        foreach ($matchingProducts as $matchingProduct) {
            $amount += $matchingProduct['quantity'];
        }

        return $amount;
    }
}
