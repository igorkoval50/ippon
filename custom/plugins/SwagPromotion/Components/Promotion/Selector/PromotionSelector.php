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

namespace SwagPromotion\Components\Promotion\Selector;

use Enlight_Components_Session_Namespace as Session;
use SwagPromotion\Components\BasketContextBuilder;
use SwagPromotion\Components\ProductMatcher;
use SwagPromotion\Components\Promotion\DiscountHandler\ProductHandler\AbsoluteProductHandler;
use SwagPromotion\Components\Promotion\PromotionDiscount;
use SwagPromotion\Components\Rules\RuleBuilder;
use SwagPromotion\Components\Services\FreeGoodsServiceInterface;
use SwagPromotion\Models\Promotion;
use SwagPromotion\Models\Repository\Repository;
use SwagPromotion\Struct\AppliedPromotions;
use SwagPromotion\Struct\Promotion as PromotionStruct;

class PromotionSelector implements Selector
{
    /**
     * @var Repository
     */
    private $promotionRepository;

    /**
     * @var ProductMatcher
     */
    private $productMatcher;

    /**
     * @var PromotionDiscount
     */
    private $promotionDiscount;

    /**
     * @var BasketContextBuilder
     */
    private $basketContextBuilder;

    /**
     * @var FreeGoodsServiceInterface
     */
    private $freeGoodsService;

    /**
     * @var Session
     */
    private $session;

    public function __construct(
        Repository $repository,
        ProductMatcher $productMatcher,
        PromotionDiscount $promotionDiscount,
        BasketContextBuilder $basketContextBuilder,
        FreeGoodsServiceInterface $freeGoodsService,
        Session $session
    ) {
        $this->promotionRepository = $repository;
        $this->productMatcher = $productMatcher;
        $this->promotionDiscount = $promotionDiscount;
        $this->basketContextBuilder = $basketContextBuilder;
        $this->freeGoodsService = $freeGoodsService;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(array $basket, $customerGroupId, $customerId, $shopId, array $voucherIds = [])
    {
        $doNotAllow = [];
        $stopProcessingPriority = null;

        /** @var PromotionStruct[] $promotions */
        $promotions = $this->promotionRepository->getActivePromotions($customerGroupId, $shopId, $voucherIds);

        $customerData = $this->basketContextBuilder->getCustomerData($customerId);
        $previousPromotionCounts = $this->promotionRepository->getPromotionCounts($customerId);

        $appliedPromotions = new AppliedPromotions();

        foreach ($promotions as $promotion) {
            $appliedPromotions->freeGoodsBadges[$promotion->id] = $promotion->freeGoodsBadgeText;
            $appliedPromotions->promotionTypes[$promotion->id] = $promotion->type;
            $appliedPromotions->freeGoodsBundleMaxQuantity[$promotion->id] = 0;

            $basketData = $this->basketContextBuilder->getBasketData();
            // quits if $stopProcessingPriority is set
            // other promotions with lower priorities are ignored for basket calculation
            if (isset($stopProcessingPriority) && $stopProcessingPriority >= $promotion->priority) {
                break;
            }

            // if an excluded promotion was run before, skip this promotion
            foreach ($promotion->doNotRunAfter as $id) {
                if (in_array((int) $id, $appliedPromotions->promotionIds, true)) {
                    continue 2;
                }
            }

            // if another promotion blacklisted this one, skip this promotion
            if (in_array($promotion->id, $doNotAllow, true)) {
                continue;
            }

            // Get context for current basket
            $products = $this->basketContextBuilder->getProductData($basket);

            // Normal case, check for the normal rules first, then check for applyRules
            if (!$promotion->applyRulesFirst) {
                if (!$this->checkRules($basketData, $products, $customerData, $promotion, $basket, $appliedPromotions)) {
                    continue;
                }

                $matches = $this->checkForMatches($products, $promotion, $basket);
                if ($matches === false) {
                    continue;
                }
            } else {
                // applyRulesFirst, first check for the matches and create a new basket context for the normal rules
                $matches = $this->checkForMatches($products, $promotion, $basket);
                if ($matches === false) {
                    continue;
                }

                $basketData = $this->createNewBasketData($matches);

                if (!$this->checkRules($basketData, $matches, $customerData, $promotion, $basket, $appliedPromotions)) {
                    continue;
                }
            }

            if ($promotion->maxUsage > 0
                && isset($previousPromotionCounts[$promotion->id])
                && $previousPromotionCounts[$promotion->id] >= $promotion->maxUsage
            ) {
                $appliedPromotions->promotionsUsedTooOften[] = $promotion;
                continue;
            }
            $applied = true;
            if (($promotion->type === PromotionStruct::TYPE_PRODUCT_ABSOLUTE
                    || $promotion->type === PromotionStruct::TYPE_PRODUCT_PERCENTAGE)
                && $promotion->discountDisplay === Promotion::DISCOUNT_DISPLAY_SINGLE) {
                foreach ($matches as $match) {
                    if (!$this->promotionDiscount->apply($promotion, $basket, [$match])) {
                        $applied = false;
                    }
                }
            } else {
                if (!$this->promotionDiscount->apply($promotion, $basket, $matches)) {
                    $applied = false;
                }
            }

            if (!$applied) {
                if ($promotion->type === PromotionStruct::TYPE_PRODUCT_FREEGOODS) {
                    $this->freeGoodsService->clearFreeGoodsFromBasket(
                        $basket['content'],
                        $promotion->freeGoods,
                        $promotion->id
                    );

                    if ($this->freeGoodsService->isAchievedStack($promotion, $matches)) {
                        $appliedPromotions->freeGoodsArticlesIds[$promotion->id] = $promotion->freeGoods;
                        $appliedPromotions->freeGoodsBundleMaxQuantity[$promotion->id] = 1;
                    }
                }

                if ($promotion->type === PromotionStruct::TYPE_PRODUCT_FREEGOODSBUNDLE) {
                    if ($this->freeGoodsService->isAchievedStack($promotion, $matches)) {
                        $appliedPromotions->freeGoodsArticlesIds[$promotion->id] = $promotion->freeGoods;
                        $appliedPromotions->freeGoodsBundleMaxQuantity[$promotion->id] = $this->getMaxQuantityForFreeGoodsBundle(
                            $matches,
                            $promotion->step,
                            $promotion->freeGoodBundleMaxQuantityCurrentSelection,
                            $promotion->maxQuantity
                        );
                    }
                }

                continue;
            }

            // Now we consider this promotion as applied
            $appliedPromotions->promotionIds[] = $promotion->id;

            // if this promotion excludes others, store the info now
            if ($promotion->doNotAllowLater) {
                foreach ($promotion->doNotAllowLater as $id) {
                    $doNotAllow[] = (int) $id;
                }
            }

            // if this promotion quits the processing, save the priority to skip promotions with lower priority
            if ($promotion->stopProcessing) {
                $stopProcessingPriority = $promotion->priority;
            }

            // stop processing, if the promotion was exclusive. As the promotion repository sorts by
            // exclusive DESC, priority DESC, we can be sure, that the exclusive promotions will always come first
            if ($promotion->exclusive) {
                break;
            }
        }

        $appliedPromotions->basket = Shopware()->Modules()->Basket()->sGetBasket();

        $this->session->offsetUnset('swag-promotion-direct-promoted-items');

        // if a free good promotion was applied, but a later promotion excludes it, the information must be deleted again.
        // as the promotion id is the array key, the flipped $doNotAllowed array can be used for that
        $appliedPromotions->freeGoodsArticlesIds = array_diff_key(
            $appliedPromotions->freeGoodsArticlesIds,
            array_flip($doNotAllow)
        );

        return $appliedPromotions;
    }

    /**
     * @return bool
     */
    private function checkRules(
        array $basketData,
        array $products,
        array $customerData,
        PromotionStruct $promotion,
        array $basket,
        AppliedPromotions $appliedPromotions
    ) {
        /** @var RuleBuilder $basketRuleBuilder */
        $basketRuleBuilder = $this->basketContextBuilder->getBasketRuleBuilder($basketData, $products, $customerData);

        // Evaluate the rule, skip if rules don't apply
        $rules = $basketRuleBuilder->fromArray($promotion->rules);

        if (!$rules->validate()) {
            if ($promotion->type === PromotionStruct::TYPE_PRODUCT_FREEGOODS
                || $promotion->type === PromotionStruct::TYPE_PRODUCT_FREEGOODSBUNDLE) {
                $this->freeGoodsService->clearFreeGoodsFromBasket(
                    $basket['content'],
                    $promotion->freeGoods,
                    $promotion->id
                );
            }

            if (!$promotion->showHintInBasket || empty($basketData) || (int) $basketData['numberOfProducts'] === 0) {
                return false;
            }

            $showHintInCart = true;
            foreach ($promotion->rules as $ruleContainer) {
                foreach ($ruleContainer as $ruleName => $rule) {
                    if (stripos($ruleName, 'customer') !== false) {
                        // The promotion is invalid and if the promotion contains a customer rule, it is very likely,
                        // that the customer could not reach a valid state. Therefore do not show a hint in the cart
                        // See SwagPromotion/Resources/views/frontend/swag_promotion/checkout/does_not_match_offcanvas.tpl
                        $showHintInCart = false;
                    }
                }
            }

            if ($showHintInCart) {
                $appliedPromotions->promotionsDoNotMatch[] = $promotion;
            }

            return false;
        }

        return true;
    }

    /**
     * @return array|bool
     */
    private function checkForMatches(array $products, PromotionStruct $promotion, array $basket)
    {
        $matches = $products;
        if ($promotion->applyRules && strpos($promotion->type, 'basket.') === false) {
            $matches = $this->productMatcher->getMatchingProducts($products, $promotion->applyRules);
        }

        // Filter to not count products, which prices are lower than the discount
        if ($promotion->type === AbsoluteProductHandler::ABSOLUTE_PRODUCT_HANDLER_NAME) {
            $matches = array_filter($matches, function ($product) use ($promotion) {
                return $product['price'] > $promotion->amount;
            });
        }

        if (empty($matches)) {
            if ($promotion->type === PromotionStruct::TYPE_PRODUCT_FREEGOODS
                || $promotion->type === PromotionStruct::TYPE_PRODUCT_FREEGOODSBUNDLE) {
                $this->freeGoodsService->clearFreeGoodsFromBasket(
                    $basket['content'],
                    $promotion->freeGoods,
                    $promotion->id
                );
            }

            return false;
        }

        return $matches;
    }

    /**
     * Create new basket context only with matches of the applyRules
     *
     * @return array
     */
    private function createNewBasketData(array $matches)
    {
        $basketData = [];

        $newBasketAmountGross = 0.00;
        $newBasketAmountNet = 0.00;
        $newBasketNumberOfProducts = 0;
        $newBasketShippingFree = '0';
        foreach ($matches as $product) {
            $newBasketAmountGross += (float) $product['price'] * (int) $product['quantity'];
            $newBasketAmountNet += (float) $product['netprice'] * (int) $product['quantity'];
            ++$newBasketNumberOfProducts;
            if ($product['detail::shippingfree']) {
                $newBasketShippingFree = $product['detail::shippingfree'];
            }
        }

        $basketData['amountGross'] = $newBasketAmountGross;
        $basketData['amountNet'] = $newBasketAmountNet;
        $basketData['numberOfProducts'] = $newBasketNumberOfProducts;
        $basketData['shippingFree'] = $newBasketShippingFree;

        return $basketData;
    }

    private function getMaxQuantityForFreeGoodsBundle(array $matches, int $step, ?int $currentSelectedFreeGoodsAmount = null, ?int $maxFreeGoodsInBasket = null): int
    {
        $step = $step < 1 ? 1 : $step;

        $quantity = 0;
        foreach ($matches as $match) {
            $quantity += (int) ($match['quantity'] / $step);
        }

        if ($maxFreeGoodsInBasket && $maxFreeGoodsInBasket < $quantity) {
            $quantity = $maxFreeGoodsInBasket;
        }

        $quantity -= $currentSelectedFreeGoodsAmount ?? 0;

        return $quantity;
    }
}
