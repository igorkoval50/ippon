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

use Doctrine\DBAL\Connection;
use Enlight_Components_Session_Namespace as Session;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Shopware\Components\Cart\BasketHelper;
use Shopware\Components\Cart\BasketHelperInterface;
use Shopware\Components\Cart\Struct\DiscountContext;
use Shopware\Components\DependencyInjection\Bridge\Config;
use Shopware_Components_Config;
use SwagPromotion\Components\Cart\BasketQueryHelperDecorator;
use SwagPromotion\Components\Promotion\CurrencyConverter;
use SwagPromotion\Models\Promotion as PromotionEntity;
use SwagPromotion\Struct\PromotedProduct;
use SwagPromotion\Struct\Promotion;

class BasketService implements BasketServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CurrencyConverter
     */
    private $currencyConverter;

    /**
     * @var BasketHelper
     */
    private $basketHelper;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var \Shopware_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(
        Connection $connection,
        Session $session,
        CurrencyConverter $currencyConverter,
        BasketHelper $basketHelper,
        Shopware_Components_Config $config,
        ContextServiceInterface $contextService,
        \Shopware_Components_Snippet_Manager $snippets
    ) {
        $this->connection = $connection;
        $this->session = $session;
        $this->currencyConverter = $currencyConverter;
        $this->basketHelper = $basketHelper;
        $this->config = $config;
        $this->contextService = $contextService;
        $this->snippets = $snippets;
    }

    /**
     * {@inheritdoc}
     */
    public function insertDiscount(Promotion $promotion, $discountGross, $discountNet, $taxRate, array $matchingProducts)
    {
        if (!$promotion->number) {
            $promotion->number = 'promotion-' . $promotion->id;
        }

        if ($promotion->type !== 'product.freegoods'
            && $this->config->get('proportionalTaxCalculation')
            && !$this->session->get('taxFree')
        ) {
            $discountContext = $this->createDiscountContext($promotion, $discountGross);
            $discountContext->addAttribute(
                BasketQueryHelperDecorator::ATTRIBUTE_COLUMN_PROMOTION_ID,
                new Attribute(['id' => $promotion->id])
            );

            if (($promotion->type === Promotion::TYPE_PRODUCT_ABSOLUTE
                    || $promotion->type === Promotion::TYPE_PRODUCT_PERCENTAGE)
                && $promotion->discountDisplay === PromotionEntity::DISCOUNT_DISPLAY_SINGLE) {
                $discountContext->setDiscountName($discountContext->getDiscountName() . ' (' . $matchingProducts[0]['ordernumber'] . ')');
            }

            $promotionProducts = array_column($matchingProducts, 'product::id');
            $discountContext->addAttribute('matching_products', new Attribute($promotionProducts));

            $this->basketHelper->addProportionalDiscount($discountContext);

            return;
        }

        if ($this->session->get('taxFree')) {
            $this->addDiscount($promotion, $discountNet, $discountNet, 0.0, $matchingProducts);

            return;
        }

        $this->addDiscount($promotion, $discountGross, $discountNet, $taxRate, $matchingProducts);
    }

    /**
     * @param PromotedProduct[] $promotedProducts
     */
    public function updatePromotedItems(Promotion $promotion, array $promotedProducts)
    {
        foreach ($promotedProducts as $promotedProduct) {
            $sql = <<<SQL
UPDATE s_order_basket_attributes
SET swag_promotion_item_discount = swag_promotion_item_discount + :discount
WHERE id = :id
SQL;

            $this->connection->executeUpdate(
                $sql,
                [
                    'discount' => round($promotedProduct->getDiscount(), 2),
                    'id' => $promotedProduct->getBasketItemAttributeId(),
                ]
            );
        }
    }

    /**
     * @param PromotedProduct[] $promotedProducts
     */
    public function insertDirectProductDiscount(Promotion $promotion, array $promotedProducts)
    {
        $discountSnippet = $this->snippets->getNamespace('frontend/swag_promotion/main')->get('promotionBadge');
        $promotedItems = [];

        if ($this->session->offsetExists('swag-promotion-direct-promoted-items')) {
            $promotedItems = $this->session->offsetGet('swag-promotion-direct-promoted-items');
        }

        foreach ($promotedProducts as $promotedProduct) {
            $sql = <<<SQL
UPDATE s_order_basket ob, s_order_basket_attributes ba
SET ob.articlename = :promotionInfo, ob.shippingfree = :shippingfree, ba.swag_promotion_direct_item_discount = ba.swag_promotion_direct_item_discount + :discount, ba.swag_promotion_direct_promotions = :appliedPromotions
WHERE ba.id = :id AND ob.id = ba.basketID
SQL;
            $info = $promotedProduct->getName();
            if (strpos($promotedProduct->getName(), '(' . $discountSnippet . ')') === false) {
                $info = $promotedProduct->getName() . ' (' . $discountSnippet . ')';
            }

            $this->connection->executeUpdate(
                $sql,
                [
                    'promotionInfo' => $info,
                    'shippingfree' => (int) $promotion->shippingFree,
                    'discount' => round($promotedProduct->getDirectDiscount(), 2),
                    'appliedPromotions' => $promotedItems[$promotedProduct->getBasketItemId()]['appliedPromotions'] ? json_encode($promotedItems[$promotedProduct->getBasketItemId()]['appliedPromotions']) : null,
                    'id' => $promotedProduct->getBasketItemAttributeId(),
                ]
            );
            $promotedItems[$promotedProduct->getBasketItemId()] = $promotedProduct->getDirectDiscount();
        }
    }

    /**
     * @param float  $discountGross
     * @param float  $discountNet
     * @param string $taxRate
     */
    private function addDiscount(Promotion $promotion, $discountGross, $discountNet, $taxRate, array $matchingProducts)
    {
        $basketQuery = $this->getBasketInsertQuery($promotion, $discountGross, $discountNet, $taxRate, $matchingProducts);
        $basketQuery->execute();

        $basketId = $this->connection->lastInsertId('s_order_basket');

        $attributeQuery = $this->getAttributeInsertQuery($basketId, $promotion->id);

        if ($promotion->type === 'basket.shippingfree') {
            $attributeQuery->setValue('swag_is_shipping_free_promotion', true);
        }

        $attributeQuery->execute();
    }

    /**
     * @param int $basketId
     * @param int $promotionId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getAttributeInsertQuery($basketId, $promotionId)
    {
        return $this->connection->createQueryBuilder()
            ->insert('s_order_basket_attributes')
            ->setValue('basketID', ':basketId')
            ->setValue('swag_promotion_id', ':promotionId')
            ->setParameters([
                'basketId' => $basketId,
                'promotionId' => $promotionId,
            ]);
    }

    /**
     * @param float  $discountGross
     * @param float  $discountNet
     * @param string $taxRate
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getBasketInsertQuery(Promotion $promotion, $discountGross, $discountNet, $taxRate, array $matchingProducts)
    {
        $userId = $this->session->get('sUserId') ?: 0;

        $discountName = $promotion->name;

        if (($promotion->type === Promotion::TYPE_PRODUCT_ABSOLUTE
                || $promotion->type === Promotion::TYPE_PRODUCT_PERCENTAGE)
            && $promotion->discountDisplay === PromotionEntity::DISCOUNT_DISPLAY_SINGLE) {
            $discountName = $promotion->name . ' (' . $matchingProducts[0]['ordernumber'] . ')';
        }

        return $this->connection->createQueryBuilder()
            ->insert('s_order_basket')
            ->setValue('sessionID', ':sessionId')
            ->setValue('userID', ':userID')
            ->setValue('articlename', ':articlename')
            ->setValue('ordernumber', ':ordernumber')
            ->setValue('price', ':price')
            ->setValue('netprice', ':netprice')
            ->setValue('tax_rate', ':tax_rate')
            ->setValue('currencyfactor', ':currencyfactor')
            ->setValue('shippingfree', ':shippingfree')
            ->setValue('articleID', 0)
            ->setValue('quantity', 1)
            ->setValue('modus', 4)
            ->setParameters([
                'sessionId' => $this->session->get('sessionId'),
                'userID' => $userId,
                'articlename' => $discountName,
                'ordernumber' => $promotion->number,
                'price' => $discountGross,
                'netprice' => $discountNet,
                'tax_rate' => $taxRate,
                'currencyfactor' => $this->currencyConverter->getFactor(),
                'shippingfree' => (int) $promotion->shippingFree,
            ]);
    }

    /**
     * @param float $discountGross
     *
     * @return DiscountContext
     */
    private function createDiscountContext(Promotion $promotion, $discountGross)
    {
        $discountContext = new DiscountContext(
            $this->session->get('sessionId'),
            BasketHelperInterface::DISCOUNT_ABSOLUTE,
            $discountGross,
            $promotion->name,
            $promotion->number,
            4, // MODE: 4 is default for promotions
            $this->currencyConverter->getFactor(),
            !$this->contextService->getShopContext()->getCurrentCustomerGroup()->displayGrossPrices()
        );

        return $discountContext;
    }
}
