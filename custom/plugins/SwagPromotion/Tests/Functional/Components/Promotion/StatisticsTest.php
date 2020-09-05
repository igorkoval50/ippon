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

namespace SwagPromotion\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagPromotion\Components\Promotion\Statistics;

class StatisticsTest extends TestCase
{
    use DatabaseTransactionBehaviour;

    public function test_findPromotionIdsInTime_invalidDates()
    {
        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->findPromotionIdsInTime(null, null);

        static::assertSame($result, []);
    }

    public function test_findPromotionIdsInTime_notMatchingDates()
    {
        $this->installPromotionsAndOrder();

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->findPromotionIdsInTime(
            (new \DateTime())->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        static::assertSame($result, []);
    }

    public function test_findPromotionIdsInTime_expectsOnlyTimeBasedResult()
    {
        $this->installPromotionsAndOrder();

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->findPromotionIdsInTime(
            '2020-02-18 00:00:00',
            '2020-03-18 00:00:00'
        );

        $expectedResult = [
            '10000',
            '10001',
            '10002',
            '10003',
        ];

        foreach ($result as $item) {
            static::assertTrue(in_array($item, $expectedResult));
        }

        static::assertCount(4, $result);
    }

    public function test_findPromotionIdsInTime_expectsTimeBasedAndNullTimeResult()
    {
        $this->installPromotionsAndOrder();
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotion_statistics_null_time.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->findPromotionIdsInTime(
            '2020-02-18 00:00:00',
            '2020-03-18 00:00:00'
        );

        $expectedResult = [
            '10000',
            '10001',
            '10002',
            '10003',
            '10010',
        ];

        foreach ($result as $item) {
            static::assertTrue(in_array($item, $expectedResult));
        }

        static::assertCount(5, $result);
    }

    public function test_getStatisticsForPromotionDetails_nullDates()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionDetails([1, 2, 3], 0, 100);

        $expectedNumbers = [
            '20003',
            '20004',
            '20005',
            '20006',
            '20065',
            '20007',
            '20008',
            '20009',
            '20010',
        ];

        foreach ($result as $item) {
            if (!in_array($item['order_number'], $expectedNumbers)) {
                static::fail(sprintf('Number %s is not expected', $item['order_number']));
            }
        }

        static::assertCount(9, $result);
    }

    public function test_getStatisticsForPromotionDetails_ExpectsTwoPromotionsAndthreeOrders()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionDetails([1, 2, 3], 0, 100, '2020-01-02', '2020-01-14');

        $expectedNumbers = [
            '20004',
            '20005',
            '20006',
        ];

        $expectedPromotionIds = [
            '1',
            '2',
        ];

        foreach ($result as $item) {
            if (!in_array($item['order_number'], $expectedNumbers)) {
                static::fail(sprintf('Number %s is not expected', $item['order_number']));
            }

            if (!in_array($item['promotion_id'], $expectedPromotionIds)) {
                static::fail(sprintf('PromotionId %s is not expected', $item['promotion_id']));
            }
        }

        static::assertCount(3, $result);
    }

    public function test_getStatisticsForPromotionDetails_ExpectsTwoPromotionsAndFourOrders()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionDetails([1, 2, 3], 0, 100, '2020-01-02', '2020-01-20');

        $expectedNumbers = [
            '20004',
            '20005',
            '20006',
            '20065',
        ];

        $expectedPromotionIds = [
            '1',
            '2',
        ];

        foreach ($result as $item) {
            if (!in_array($item['order_number'], $expectedNumbers)) {
                static::fail(sprintf('Number %s is not expected', $item['order_number']));
            }

            if (!in_array($item['promotion_id'], $expectedPromotionIds)) {
                static::fail(sprintf('PromotionId %s is not expected', $item['promotion_id']));
            }
        }

        static::assertCount(4, $result);
    }

    public function test_getStatisticsForPromotionDetails_ExpectsOnePromotionsAndFourOrders()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionDetails([1, 2, 3], 0, 100, '2020-02-01', '2020-02-28');

        $expectedNumbers = [
            '20007',
            '20008',
            '20009',
            '20010',
        ];

        $expectedPromotionIds = [
            '3',
        ];

        foreach ($result as $item) {
            if (!in_array($item['order_number'], $expectedNumbers)) {
                static::fail(sprintf('Number %s is not expected', $item['order_number']));
            }

            if (!in_array($item['promotion_id'], $expectedPromotionIds)) {
                static::fail(sprintf('PromotionId %s is not expected', $item['promotion_id']));
            }
        }

        static::assertCount(4, $result);
    }

    public function test_getStatisticsForPromotionList_nullDates()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionList([1, 2, 3]);

        $expected = [
            'promotion1' => [
                'turnover' => 584.4000000000001,
                'orders' => 3,
                'name' => 'promotion 1',
            ],
            'promotion2' => [
                'turnover' => 389.6,
                'orders' => 2,
                'name' => 'promotion 2',
            ],
            'promotion3' => [
                'turnover' => 779.2,
                'orders' => 4,
                'name' => 'promotion 3',
            ],
        ];

        foreach ($result as $item) {
            if (!array_key_exists($item['name'], $expected)) {
                static::fail(sprintf('Promotion with name: %s is not expected', $item['name']));
            }

            $expectedPromotionData = $expected[$item['name']];
            static::assertSame($expectedPromotionData['turnover'], $item['turnover']);
            static::assertSame($expectedPromotionData['orders'], $item['orders']);
        }

        static::assertCount(3, $result);
    }

    public function test_getStatisticsForPromotionList_ExpectsTwoPromotionsAndThreeOrders()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionList([1, 2, 3], '2020-01-02', '2020-01-14');

        $expected = [
            'promotion1' => [
                    'turnover' => 389.6,
                    'orders' => 2,
                    'name' => 'promotion1',
                ],
            'promotion2' => [
                    'turnover' => 194.8,
                    'orders' => 1,
                    'name' => 'promotion2',
                ],
        ];

        foreach ($result as $item) {
            if (!array_key_exists($item['name'], $expected)) {
                static::fail(sprintf('Promotion with name: %s is not expected', $item['name']));
            }

            $expectedPromotionData = $expected[$item['name']];
            static::assertSame($expectedPromotionData['turnover'], $item['turnover']);
            static::assertSame($expectedPromotionData['orders'], $item['orders']);
        }

        static::assertCount(2, $result);
    }

    public function test_getStatisticsForPromotionList_ExpectsTwoPromotionsAndFourOrders()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionList([1, 2, 3], '2020-01-02', '2020-01-20');

        $expected = [
            'promotion1' => [
                'turnover' => 389.6,
                'orders' => 2,
                'name' => 'promotion1',
            ],
            'promotion2' => [
                'turnover' => 389.6,
                'orders' => 2,
                'name' => 'promotion2',
            ],
        ];

        foreach ($result as $item) {
            if (!array_key_exists($item['name'], $expected)) {
                static::fail(sprintf('Promotion with name: %s is not expected', $item['name']));
            }

            $expectedPromotionData = $expected[$item['name']];
            static::assertSame($expectedPromotionData['turnover'], $item['turnover']);
            static::assertSame($expectedPromotionData['orders'], $item['orders']);
        }

        static::assertCount(2, $result);
    }

    public function test_getStatisticsForPromotionList_ExpectsOnePromotionsAndFourOrders()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotions_orders.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $statisticsService = $this->getStatisticsService();

        $result = $statisticsService->getStatisticsForPromotionList([1, 2, 3], '2020-02-01', '2020-02-28');

        $expected = [
            'promotion3' => [
                'turnover' => 779.2,
                'orders' => 4,
                'name' => 'promotion3',
            ],
        ];

        foreach ($result as $item) {
            if (!array_key_exists($item['name'], $expected)) {
                static::fail(sprintf('Promotion with name: %s is not expected', $item['name']));
            }

            $expectedPromotionData = $expected[$item['name']];
            static::assertSame($expectedPromotionData['turnover'], $item['turnover']);
            static::assertSame($expectedPromotionData['orders'], $item['orders']);
        }

        static::assertCount(1, $result);
    }

    private function getStatisticsService(): Statistics
    {
        return Shopware()->Container()->get('swag_promotion.statistics');
    }

    private function installPromotionsAndOrder()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/promotion_statistics.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }
}
