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
use SwagFuzzy\Components\SettingsService;
use SwagFuzzy\Components\SimilarResultsService;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class SimilarResultsServiceTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_getSimilarResults()
    {
        $this->insertDemoData();

        $expected = [
            [
                'searchTerm' => 'bad bag',
                'searchesCount' => 4,
                'lastSearchDate' => '2016-08-30 12:04:23',
                'resultsCount' => 5,
            ],
        ];

        $settingsServiceMock = $this->getSettingsServiceMock();

        $service = new SimilarResultsService(
            Shopware()->Container()->get('dbal_connection'),
            Shopware()->Container()->get('shopware_searchdbal.search_term_helper'),
            $settingsServiceMock
        );

        $keywords = ['strandbag', 'baking', 'back', 'basic', 'battery', 'backform', 'balmoral'];

        $result = $service->getSimilarResults('bag', $keywords, 1);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSettingsServiceMock()
    {
        $settingsServiceMock = $this->getMockBuilder(SettingsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $settingsServiceMock->method('getSettings')
            ->willReturn(['maxKeywordsAndSimilarWords' => 30]);

        return $settingsServiceMock;
    }

    private function insertDemoData()
    {
        $sql = <<<'SQL'
          INSERT INTO s_plugin_swag_fuzzy_statistics 
          (shopId, searchTerm, lastSearchDate, resultsCount, searchesCount) 
          VALUES
          (1, 'bad bag', '2016-08-30 12:04:23', 5, 4)
SQL;
        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }
}
