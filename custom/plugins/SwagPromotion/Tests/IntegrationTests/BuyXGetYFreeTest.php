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

use sBasket;
use Shopware\Components\DependencyInjection\Container;
use SwagPromotion\Models\Repository\DummyRepository;
use SwagPromotion\Struct\Promotion;
use SwagPromotion\Tests\DatabaseTestCaseTrait;
use SwagPromotion\Tests\Helper\PromotionFactory;
use SwagPromotion\Tests\PromotionTestCase;

/**
 * @medium
 * @group integration
 */
class BuyXGetYFreeTest extends PromotionTestCase
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

        $this->container = Shopware()->Container();
        $this->basket = $this->container->get('modules')->Basket();
        $this->basket->sDeleteBasket();
        Shopware()->Front()->setRequest(new \Enlight_Controller_Request_RequestHttp());
    }

    public function testBuy5GetYFreeWith0FreeAndOnePosition()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 1);

        $this->basket->sAddArticle('SW10009', 4); // 24.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 4;
        $discount = 0;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);

        $this->setBuyXGetYFreePromotionRepository(5, 3);
        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5GetYFreeWith0FreeAndMorePositions()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 1);

        $this->basket->sAddArticle('SW10009', 1); // 24.99
        $this->basket->sAddArticle('SW10013', 1); //  2.50
        $this->basket->sAddArticle('SW10137', 2); // 59.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 + 2.50 + 59.99 * 2;
        $discount = 0;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);

        $this->setBuyXGetYFreePromotionRepository(5, 3);
        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get1FreeWith1FreeAndOnePosition()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 1);

        $this->basket->sAddArticle('SW10009', 5); // 24.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 5;
        $discount = 24.99;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get1FreeWith1FreeAndMorePositions()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 1);

        $this->basket->sAddArticle('SW10009', 3); // 24.99
        $this->basket->sAddArticle('SW10013', 1); //  2.50
        $this->basket->sAddArticle('SW10137', 3); // 59.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 3 + 2.50 + 59.99 * 3;
        $discount = 2.50;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get1FreeWith3FreeAndOnePosition()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 1);

        $this->basket->sAddArticle('SW10009', 17); // 24.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 17;
        $discount = 24.99 * 3;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get1FreeWith3FreeAndMorePositions()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 1);

        $this->basket->sAddArticle('SW10009', 9); // 24.99
        $this->basket->sAddArticle('SW10013', 2); //  2.50
        $this->basket->sAddArticle('SW10137', 6); // 59.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 9 + 2.50 * 2 + 59.99 * 6;
        $discount = 2.50 * 2 + 24.99;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get3FreeWith1FreeAndOnePosition()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 3);

        $this->basket->sAddArticle('SW10009', 5); // 24.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 5;
        $discount = 24.99 * 3;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get3FreeWith1FreeAndMorePositions()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 3);

        $this->basket->sAddArticle('SW10009', 3); // 24.99
        $this->basket->sAddArticle('SW10013', 1); //  2.50
        $this->basket->sAddArticle('SW10137', 3); // 59.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 3 + 2.50 + 59.99 * 3;
        $discount = 24.99 * 2 + 2.50;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get3FreeWith3FreeAndOnePosition()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 3);

        $this->basket->sAddArticle('SW10009', 18); // 24.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 18;
        $discount = 24.99 * 9;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get3FreeWith3FreeAndMorePositions()
    {
        $this->setBuyXGetYFreePromotionRepository(5, 3);

        $this->basket->sAddArticle('SW10009', 1); // 24.99
        $this->basket->sAddArticle('SW10013', 6); //  2.50
        $this->basket->sAddArticle('SW10137', 8); // 59.99
        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 * 1 + 2.50 * 6 + 59.99 * 8;
        $discount = 24.99 * 1 + 2.50 * 6 + 59.99 * 2;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get1FreeWith0FreeAndFreeGoods()
    {
        $promotionRepo = $this->setBuyXGetYFreePromotionRepository(
            5,
            1,
            PromotionFactory::create([
                'number' => 'freeGoodsXY1',
                'type' => 'product.freegoods',
                'freeGoods' => [6], // 6 = cigar special -> 35.95
            ])
        );

        $this->basket->sAddArticle('SW10009', 1); // 24.99
        $this->basket->sAddArticle('SW10013', 2); //  2.50
        $this->basket->sAddArticle('SW10137', 1); // 59.99

        /** @var Promotion $promotion */
        foreach ($promotionRepo->getActivePromotions() as $promotion) {
            if ($promotion->type === 'product.freegoods') {
                $promotionId = $promotion->id;
                break;
            }
        }

        // now adding as a freeGood explicitly
        $this->container->get('swag_promotion.service.free_goods_service')
            ->addArticleAsFreeGood('SW10006', $promotionId);

        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 + 2.50 * 2 + 59.99 + 35.95;
        $discount = 35.95;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get1FreeWith1FreeAndFreeGoods()
    {
        $promotionRepo = $this->setBuyXGetYFreePromotionRepository(
            5,
            1,
            PromotionFactory::create([
                'number' => 'freeGoodsXY1',
                'type' => 'product.freegoods',
                'freeGoods' => [6], // 6 = cigar special -> 35.95
            ])
        );

        $this->basket->sAddArticle('SW10009', 1); // 24.99
        $this->basket->sAddArticle('SW10013', 4); //  2.50
        $this->basket->sAddArticle('SW10137', 1); // 59.99

        /** @var Promotion $promotion */
        foreach ($promotionRepo->getActivePromotions() as $promotion) {
            if ($promotion->type === 'product.freegoods') {
                $promotionId = $promotion->id;
                break;
            }
        }

        // now adding as a freeGood explicitly
        $this->container->get('swag_promotion.service.free_goods_service')
            ->addArticleAsFreeGood('SW10006', $promotionId);

        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 + 2.50 * 4 + 59.99 + 35.95;
        $discount = 2.50 + 35.95;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    public function testBuy5Get3FreeWith1FreeAndFreeGoods()
    {
        $promotionRepo = $this->setBuyXGetYFreePromotionRepository(
            5,
            3,
            PromotionFactory::create([
                'number' => 'freeGoodsXY1',
                'type' => 'product.freegoods',
                'freeGoods' => [6], // 6 = cigar special -> 35.95
            ])
        );

        $this->basket->sAddArticle('SW10009', 1); // 24.99
        $this->basket->sAddArticle('SW10013', 4); //  2.50
        $this->basket->sAddArticle('SW10137', 1); // 59.99

        /** @var Promotion $promotion */
        foreach ($promotionRepo->getActivePromotions() as $promotion) {
            if ($promotion->type === 'product.freegoods') {
                $promotionId = $promotion->id;
                break;
            }
        }

        // now adding as a freeGood explicitly
        $this->container->get('swag_promotion.service.free_goods_service')
            ->addArticleAsFreeGood('SW10006', $promotionId);

        $resultBasket = $this->basket->sGetBasket();

        $usualTotal = 24.99 + 2.50 * 4 + 59.99 + 35.95;
        $discount = 2.50 * 3 + 35.95;

        static::assertEqualsWithDelta($usualTotal - $discount, $resultBasket['AmountNumeric'], 0.01);
    }

    private function setBuyXGetYFreePromotionRepository($buyX, $getYFree, $additionalPromotion = null)
    {
        /** @var DummyRepository $promotionRepo */
        $promotionRepo = $this->container->get('swag_promotion.repository');
        $promotionRepo->set(
            [
                PromotionFactory::create([
                    'number' => 'buyxgetx1',
                    'type' => 'product.buyxgetyfree',
                    'step' => $buyX,
                    'amount' => $getYFree,
                ]),
                $additionalPromotion,
            ]
        );

        return $promotionRepo;
    }
}
