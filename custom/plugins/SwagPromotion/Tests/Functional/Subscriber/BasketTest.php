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

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use SwagPromotion\Subscriber\Basket;
use SwagPromotion\Tests\DatabaseTestCaseTrait;

class BasketTest extends TestCase
{
    use DatabaseTestCaseTrait;

    public function test_onCheckBasketForProduct_shouldAddNothing(): void
    {
        $subscriber = $this->getBasketSubscriber();

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
        $expectedResult = 'WHERE promotion_basket_attr.swag_is_free_good_by_promotion_id IS NULL';
        $args = new \Enlight_Event_EventArgs(
            ['queryBuilder' => $queryBuilder]
        );

        $subscriber->onCheckBasketForProduct($args);

        $result = $queryBuilder->getSQL();

        static::assertStringEndsWith($expectedResult, $result);
    }

    public function test_onCheckBasketForProduct_shouldAddQueryExtension(): void
    {
        $subscriber = $this->getBasketSubscriber();

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

        $args = new \Enlight_Event_EventArgs(
            ['queryBuilder' => $queryBuilder]
        );

        Shopware()->Container()->get('session')->offsetSet('freeGoodsOrderNumber', '0815-4711');
        $subscriber->onCheckBasketForProduct($args);

        $expected = 'WHERE promotion_basket_attr.swag_is_free_good_by_promotion_id IS NOT NULL';
        $result = $queryBuilder->getSQL();

        static::assertStringEndsWith($expected, $result);
    }

    private function getBasketSubscriber(): Basket
    {
        return new Basket(
            Shopware()->Container()->get('dbal_connection'),
            Shopware()->Container()->get('session')
        );
    }
}
