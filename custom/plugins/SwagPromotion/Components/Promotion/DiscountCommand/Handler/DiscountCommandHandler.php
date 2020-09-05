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

namespace SwagPromotion\Components\Promotion\DiscountCommand\Handler;

use SwagPromotion\Components\Promotion\DiscountCommand\Command\Command;
use SwagPromotion\Components\Promotion\DiscountCommand\Command\DiscountCommand;
use SwagPromotion\Components\Promotion\Tax;
use SwagPromotion\Components\Services\BasketServiceInterface;
use SwagPromotion\Models\Promotion as PromotionEntity;
use SwagPromotion\Struct\Promotion;

/**
 * Class DiscountCommandHandler handles DiscountCommand objects and inserts the
 * discount that is specified by the given command
 */
class DiscountCommandHandler implements CommandHandler
{
    /**
     * @var float|null
     */
    private $basketAmount = null;

    /**
     * @var array
     */
    private $processed = [];

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
        /** @var DiscountCommand $command */
        $amount = $command->getAmount();

        // Do not insert discounts with amount 0
        if (!$amount) {
            return false;
        }
        $result = $this->taxCalculator->calculate(-$amount, $basket, $matchingProducts);

        if (!$this->verifyInsert($promotion, $amount, $basket)) {
            return false;
        }

        if (($promotion->type === Promotion::TYPE_PRODUCT_ABSOLUTE
            || $promotion->type === Promotion::TYPE_PRODUCT_PERCENTAGE)
            && $promotion->discountDisplay === PromotionEntity::DISCOUNT_DISPLAY_DIRECT) {
            $this->basketService->insertDirectProductDiscount($promotion, $command->getPromotedProducts());
        } else {
            $this->basketService->insertDiscount($promotion, -$amount, $result['net'], $result['taxRate'], $matchingProducts);
        }

        if (!empty($command->getPromotedProducts())) {
            $this->basketService->updatePromotedItems($promotion, $command->getPromotedProducts());
        }

        return true;
    }

    /**
     * {@inheritdoc}
     **/
    public function supports($name)
    {
        return $name === DiscountCommand::DISCOUNT_COMMAND_NAME;
    }

    /**
     * Verify if the promotion may be applied without having the total basket sum being negative.
     *
     * @param float $amount
     *
     * @return bool
     */
    private function verifyInsert(Promotion $promotion, $amount, array $basket)
    {
        foreach ($basket['content'] as $basketItem) {
            $unserializedPromotionIds = unserialize($basketItem['isFreeGoodByPromotionId']);
            if (!empty($unserializedPromotionIds)) {
                $basket['AmountNumeric'] = $basket['AmountNumeric'] - $basketItem['priceNumeric'];
            }
        }

        //If the basketAmount has never been set or if the promotion got processed already, reset the value
        if ($this->basketAmount === null || $this->processed[$promotion->id]) {
            $this->basketAmount = $basket['AmountNumeric'];
        }

        if ($this->basketAmount === $basket['AmountNumeric']) {
            $this->processed = [];
        }

        $calculatedAmount = $this->basketAmount - $amount;

        if ($calculatedAmount < 0) {
            return false;
        }

        $this->basketAmount = $calculatedAmount;
        $this->processed[$promotion->id] = $promotion->id;

        return true;
    }
}
