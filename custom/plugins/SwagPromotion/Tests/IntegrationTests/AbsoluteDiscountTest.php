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

namespace SwagPromotion\Tests\IntegrationTests;

use Enlight_Components_Session_Namespace;
use sBasket;
use Shopware\Components\DependencyInjection\Container;
use SwagPromotion\Subscriber\PromotionSubscriber;
use SwagPromotion\Tests\DatabaseTestCaseTrait;
use SwagPromotion\Tests\Helper\PromotionFactory;
use SwagPromotion\Tests\PromotionTestCase;

/**
 * @medium
 * @group integration
 */
class AbsoluteDiscountTest extends PromotionTestCase
{
    use DatabaseTestCaseTrait;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var sBasket
     */
    private $basket;

    public function setUp(): void
    {
        parent::setUp();

        Shopware()->Front()->setRequest($this->Request());
        $this->container = Shopware()->Container();
        $this->basket = $this->container->get('modules')->Basket();
        $this->basket->sDeleteBasket();
    }

    public function testAbsoluteDiscount()
    {
        $sql = file_get_contents(__DIR__ . '/Fixtures/absoluteDiscount.sql');
        $this->container->get('dbal_connection')->exec($sql);

        /** @var Enlight_Components_Session_Namespace $session */
        $session = $this->container->get('session');
        $session->offsetSet('sUserId', 1);
        $session->offsetSet('sessionId', 'sessionId');
        $session->offsetSet('sUserPassword', 'a256a310bc1e5db755fd392c524028a8');
        $session->offsetSet('sUserMail', 'test@example.com');

        $arguments = new AbsoluteDiscountTestArgumentsMock($this, []);
        $arguments->setReturn(Shopware()->Modules()->Basket()->sGetBasket());

        $subscriber = new PromotionSubscriber(
            $this->container->get('dbal_connection'),
            $this->container->get('template'),
            $this->container->get('config'),
            $this->container->get('session'),
            $this->container->get('shopware_storefront.context_service'),
            $this->container->get('swag_promotion.service.article_service'),
            $this->container->get('swag_promotion.promotion_selector')
        );
        $subscriber->afterGetBasket($arguments);

        $basket = $arguments->getReturn();

        static::assertTrue(
            abs($basket['AmountNumeric'] - (19.95 - 5)) <= 0.01,
            "Unexpected basket amount: {$basket['AmountNumeric']}"
        );
    }

    public function testShouldNotApply()
    {
        $this->setPromotionRepository(20);

        $this->basket->sAddArticle('SW10010', 1);
        $basket = $this->basket->sGetBasket();

        // 5.95 is the default price + 5 surcharge for small bassket
        // Promotion should not apply, as the basket amount < 20
        static::assertTrue(
            abs($basket['AmountNumeric'] - (10.95)) <= 0.01,
            "Unexpected basket amount: {$basket['AmountNumeric']}"
        );
    }

    public function testAbsoluteProductDiscountWithTooCheapItemsAndTooLowSum()
    {
        $this->setPromotionRepository(35, true);

        $this->basket->sAddArticle('SW10009', 1); // 24.99
        $this->basket->sAddArticle('SW10013', 4); //  2.50
        $resultBasket = $this->basket->sGetBasket();

        // Below 35€ -> No Discount
        $usualTotal = 24.99 * 1 + 2.50 * 4;
        $discount = 0;
        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testAbsoluteProductDiscountWithTooCheapItemsAndOneProduct()
    {
        $this->setPromotionRepository(35, true);

        $this->basket->sAddArticle('SW10009', 1); // 24.99
        $this->basket->sAddArticle('SW10013', 5); //  2.50
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 1 + 2.50 * 5;
        $discount = 20;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testAbsoluteProductDiscountWithTooCheapItemsAndThreeProducts()
    {
        $this->setPromotionRepository(35, true);

        $this->basket->sAddArticle('SW10009', 3); // 24.99
        $this->basket->sAddArticle('SW10013', 5); //  2.50
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 3 + 2.50 * 5;
        $discount = 40;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function test_getBaseProducts_should_accept_string_with_comma()
    {
        $promotion = [
            'id' => 9999,
            'type' => 'product.absolute',
            'rules' => [
                'and' => [
                    'true1' => [],
                ],
            ],
            'applyRules' => [
                'and' => [
                    'productCompareRule0.7336643077575902' => [
                        0 => 'price::price',
                        1 => '>=',
                        2 => '9,99',
                    ],
                ],
            ],
        ];

        $promotionRepo = $this->container->get('swag_promotion.repository');
        $promotionRepo->set([PromotionFactory::create(
            $promotion
        )]);

        Shopware()->Session()->offsetSet('sessionId', 'test-session');

        Shopware()->Modules()->Basket()->sAddArticle('SW10239');
        Shopware()->Modules()->Basket()->sRefreshBasket();
        $basket = Shopware()->Modules()->Basket()->sGetBasket()['content'];

        $foundPromotion = false;
        foreach ($basket as $basketItem) {
            if ($basketItem['ordernumber'] == 'number9999') {
                $foundPromotion = true;
                break;
            }
        }

        static::assertTrue($foundPromotion);
    }

    public function test_getBaseProducts_should_accept_string_with_dot()
    {
        $promotion = [
            'id' => 9999,
            'type' => 'product.absolute',
            'rules' => [
                'and' => [
                    'true1' => [],
                ],
            ],
            'applyRules' => [
                'and' => [
                    'productCompareRule0.7336643077575902' => [
                        0 => 'price::price',
                        1 => '>=',
                        2 => '9.99',
                    ],
                ],
            ],
        ];

        $promotionRepo = $this->container->get('swag_promotion.repository');
        $promotionRepo->set([PromotionFactory::create(
            $promotion
        )]);

        Shopware()->Session()->offsetSet('sessionId', 'test-session');

        Shopware()->Modules()->Basket()->sAddArticle('SW10239');
        Shopware()->Modules()->Basket()->sRefreshBasket();
        $basket = Shopware()->Modules()->Basket()->sGetBasket()['content'];

        $foundPromotion = false;
        foreach ($basket as $basketItem) {
            if ($basketItem['ordernumber'] == 'number9999') {
                $foundPromotion = true;
                break;
            }
        }

        static::assertTrue($foundPromotion);
    }

    private function setPromotionRepository($amountGross, $tooCheapItemsOptions = false)
    {
        $params = [
            'amount' => 3,
            'rules' => ['and' => ['basketCompareRule' => ['amountGross', '>', $amountGross]]],
        ];
        if ($tooCheapItemsOptions) {
            $params['number'] = 'prodAbs1';
            $params['type'] = 'product.absolute';
            $params['amount'] = 20;
            $params['maxQuantity'] = 2;
        }

        $promotionRepo = $this->container->get('swag_promotion.repository');
        $promotionRepo->set([PromotionFactory::create($params)]);
    }
}

class AbsoluteDiscountTestArgumentsMock extends \Enlight_Hook_HookArgs
{
    /**
     * @var mixed
     */
    public $returnData;

    public function setReturn($data)
    {
        $this->returnData = $data;
    }

    public function getReturn()
    {
        return $this->returnData;
    }
}
