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

namespace SwagFuzzy\Tests\Functional\Components;

use PHPUnit\Framework\TestCase;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagFuzzy\Components\StatisticsService;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class StatisticsServiceTest extends TestCase
{
    use DatabaseTransactionBehaviour;
    use KernelTestCaseTrait;

    public function test_applyPeriodSearchCount(): void
    {
        $connection = Shopware()->Container()->get('dbal_connection');

        $sql = file_get_contents(__DIR__ . '/_fixtures/search_term_statistic.sql');
        $connection->exec($sql);

        $data = [
            'st1' => ['searchTerm' => 'unitTest', 'shopId' => 1],
            'st2' => ['searchTerm' => 'fooBar', 'shopId' => 1],
            'st3' => ['searchTerm' => 'lorem ipsum', 'shopId' => 1],
            'st4' => ['searchTerm' => 'not present search term', 'shopId' => 1],
        ];

        $from = new \DateTime('1970-03-01');
        $to = new \DateTime('1970-06-30');

        $service = new StatisticsService($connection);
        $result = $service->applyPeriodSearchCount($data, $from, $to);

        static::assertEquals(4, $result['st1']['currentCount']);
        static::assertEquals(4, $result['st2']['currentCount']);
        static::assertEquals(4, $result['st3']['currentCount']);
        // make sure that if there is no result, the 0 is assigned
        static::assertEquals(0, $result['st4']['currentCount']);
    }
}
