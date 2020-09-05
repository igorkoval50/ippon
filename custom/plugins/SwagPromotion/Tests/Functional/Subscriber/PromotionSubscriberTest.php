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

namespace SwagPromotion\Tests\Functional\Subscriber;

use Enlight_Components_Session_Namespace;
use Shopware\Components\DependencyInjection\Container;
use SwagPromotion\Struct\AppliedPromotions;
use SwagPromotion\Struct\Promotion as PromotionStruct;
use SwagPromotion\Subscriber\PromotionSubscriber;
use SwagPromotion\Tests\DatabaseTestCaseTrait;
use SwagPromotion\Tests\PromotionTestCase;

class PromotionSubscriberTest extends PromotionTestCase
{
    use DatabaseTestCaseTrait;

    /**
     * @var Container
     */
    private $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = Shopware()->Container();
    }

    public function test_getDispatchBasketFilterSql()
    {
        $subscriber = $this->getSubscriber();

        $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();

        $eventArgs = new \Enlight_Event_EventArgs();
        $eventArgs->offsetSet('queryBuilder', $queryBuilder);
        $eventArgs->offsetSet('amount', 100);
        $eventArgs->offsetSet('amount_net', 80);

        $subscriber->getDispatchBasketFilterSql($eventArgs);

        $expectedResult = $this->normalizeString('SELECT SUM(IF(
            b.modus=0 AND oba.swag_is_free_good_by_promotion_id IS NULL,100/b.currencyFactor,0)) as amount, SUM(
            IF(b.modus=0 AND oba.swag_is_free_good_by_promotion_id IS NULL,80/b.currencyFactor,0)) as amount_net'
        );

        static::assertSame($expectedResult, $queryBuilder->getSQL());
    }

    public function test_getAmountArticlesFilterSql()
    {
        $subscriber = $this->getSubscriber();

        $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();

        $eventArgs = new \Enlight_Event_EventArgs();
        $eventArgs->offsetSet('queryBuilder', $queryBuilder);

        $subscriber->getAmountArticlesFilterSql($eventArgs);

        $expectedStart = 'SELECT';
        $expectedEnd = 'WHERE oba.swag_is_free_good_by_promotion_id IS NULL';

        static::assertStringStartsWith($expectedStart, $queryBuilder->getSQL());
        static::assertStringEndsWith($expectedEnd, $queryBuilder->getSQL());
    }

    public function test_getBasketAmountFilterSql()
    {
        $subscriber = $this->getSubscriber();

        $string = 'FROM s_order_basket WHERE sessionID = ? AND modus != 4';

        $eventArgs = new \Enlight_Event_EventArgs();
        $eventArgs->setReturn($string);

        $subscriber->getBasketAmountFilterSql($eventArgs);

        $expectedResult = $this->normalizeString('FROM s_order_basket AS b
            LEFT JOIN s_order_basket_attributes AS ba
            ON b.id = ba.basketID WHERE b.sessionID = ? AND b.modus != 4 AND ba.swag_is_free_good_by_promotion_id IS NULL'
        );

        $result = $this->normalizeString($eventArgs->getReturn());

        static::assertSame($expectedResult, $result);
    }

    public function test_beforeGetBasket()
    {
        $sql = file_get_contents(__DIR__ . '/Fixtures/basket1.sql');
        $this->container->get('dbal_connection')->exec($sql);

        /** @var Enlight_Components_Session_Namespace $session */
        $session = $this->container->get('session');
        $session->offsetSet('sUserId', 1);
        $session->offsetSet('sessionId', 'sessionId');
        $session->offsetSet('sUserPassword', 'a256a310bc1e5db755fd392c524028a8');
        $session->offsetSet('sUserMail', 'test@example.com');

        $subscriber = $this->getSubscriber();
        $subscriber->beforeGetBasket();

        $sql = "SELECT * FROM s_order_basket WHERE sessionID = 'sessionId'";
        $result = $this->container->get('dbal_connection')->fetchAll($sql);

        static::assertEmpty($result);
    }

    public function test_mergeFreeGoods_typeFreeGood()
    {
        $maxQuantity = 5;
        $appliedPromotions = $this->getAppliedPromotion($maxQuantity);

        [$freeGoods, $articleData] = $this->getProductData();

        $reflectionMethod = (new \ReflectionClass(PromotionSubscriber::class))->getMethod('mergeFreeGoods');
        $reflectionMethod->setAccessible(true);

        $subscriber = $this->getSubscriber();
        $promotionId = 1;

        $result = $reflectionMethod->invoke($subscriber, $appliedPromotions, $promotionId, $freeGoods, $articleData);

        if ($result['A']['maxQuantity'] !== 1) {
            static::fail(sprintf('Expected max quantity: %s does not match: %s in result A', 1, $result['A']['maxQuantity']));
        }

        if ($result['B']['maxQuantity'] !== 1) {
            static::fail(sprintf('Expected max quantity: %s does not match: %s in result B', 1, $result['B']['maxQuantity']));
        }

        if ($result['C']['maxQuantity'] !== 1) {
            static::fail(sprintf('Expected max quantity: %s does not match: %s in result C', 1, $result['C']['maxQuantity']));
        }

        static::assertCount(6, $result);
    }

    public function test_mergeFreeGoods_typeFreeGoodBundle()
    {
        $maxQuantity = 5;
        $appliedPromotions = $this->getAppliedPromotion($maxQuantity);

        [$freeGoods, $articleData] = $this->getProductData();

        $reflectionMethod = (new \ReflectionClass(PromotionSubscriber::class))->getMethod('mergeFreeGoods');
        $reflectionMethod->setAccessible(true);

        $subscriber = $this->getSubscriber();
        $promotionId = 2;

        $result = $reflectionMethod->invoke($subscriber, $appliedPromotions, $promotionId, $freeGoods, $articleData);

        if ($result['A']['maxQuantity'] !== $maxQuantity) {
            static::fail(sprintf('Expected max quantity: %s does not match: %s in result A', $maxQuantity, $result['A']['maxQuantity']));
        }

        if ($result['B']['maxQuantity'] !== $maxQuantity) {
            static::fail(sprintf('Expected max quantity: %s does not match: %s in result B', $maxQuantity, $result['B']['maxQuantity']));
        }

        if ($result['C']['maxQuantity'] !== $maxQuantity) {
            static::fail(sprintf('Expected max quantity: %s does not match: %s in result C', $maxQuantity, $result['C']['maxQuantity']));
        }

        static::assertCount(6, $result);
    }

    /**
     * @dataProvider updateFreeGoodMaxQuantityTest_dataProvider
     */
    public function test_updateFreeGoodMaxQuantity(array $freeGoods, int $freeGoodsBundleMaxQuantity, int $expectedQuantity)
    {
        $reflectionMethod = (new \ReflectionClass(PromotionSubscriber::class))->getMethod('updateFreeGoodMaxQuantity');
        $reflectionMethod->setAccessible(true);

        $subscriber = $this->getSubscriber();
        $result = $reflectionMethod->invoke($subscriber, $freeGoods, $freeGoodsBundleMaxQuantity);

        static::assertSame($expectedQuantity, array_shift($result)['maxQuantity']);
    }

    public function updateFreeGoodMaxQuantityTest_dataProvider()
    {
        return [
            [[['laststock' => false, 'instock' => 20]], 0,  0],
            [[['laststock' => true, 'instock' => 20]],  3,  3],
            [[['laststock' => false, 'instock' => 20]], 99, 99],
            [[['laststock' => true, 'instock' => 2]],   99, 2],
            [[['laststock' => false, 'instock' => 2]],  5,  5],
        ];
    }

    private function getAppliedPromotion(int $maxQuantity)
    {
        $appliedPromotions = new AppliedPromotions();
        $appliedPromotions->promotionIds = [1, 2];
        $appliedPromotions->promotionTypes = [
            1 => PromotionStruct::TYPE_PRODUCT_FREEGOODS,
            2 => PromotionStruct::TYPE_PRODUCT_FREEGOODSBUNDLE,
        ];

        $appliedPromotions->freeGoodsBundleMaxQuantity = [
            1 => $maxQuantity,
            2 => $maxQuantity,
        ];

        return $appliedPromotions;
    }

    private function getProductData(): array
    {
        return [
            [
                1 => ['product 1', 'laststock' => false, 'maxQuantity' => 1],
                2 => ['product 2', 'laststock' => false, 'maxQuantity' => 1],
                3 => ['product 3', 'laststock' => false, 'maxQuantity' => 1],
            ],

            [
               'A' => ['product 10', 'laststock' => false, 'maxQuantity' => 1],
               'B' => ['product 20', 'laststock' => false, 'maxQuantity' => 1],
               'C' => ['product 30', 'laststock' => false, 'maxQuantity' => 1],
            ],
        ];
    }

    private function getSubscriber()
    {
        return new PromotionSubscriberMock(
            $this->container->get('dbal_connection'),
            $this->container->get('template'),
            $this->container->get('config'),
            $this->container->get('session'),
            $this->container->get('shopware_storefront.context_service'),
            $this->container->get('swag_promotion.service.article_service'),
            $this->container->get('swag_promotion.promotion_selector')
        );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function normalizeString($string)
    {
        return trim(preg_replace('/\s{2,}|\n|\t/', '', $string));
    }
}

class PromotionSubscriberMock extends PromotionSubscriber
{
    /**
     * @var bool
     */
    public $ignoreBasketRefresh = false;

    /**
     * @return bool
     */
    public function basketRefreshNeeded()
    {
        return true;
    }
}

class PromotionSubscriberTestHookArgsMock extends \Enlight_Hook_HookArgs
{
    /**
     * @var mixed
     */
    public $return;

    public function getReturn()
    {
        return $this->return;
    }

    public function setReturn($return)
    {
        $this->return = $return;
    }
}
