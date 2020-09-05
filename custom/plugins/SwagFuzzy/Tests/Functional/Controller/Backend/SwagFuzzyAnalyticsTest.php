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

namespace SwagFuzzy\Tests\Functional\Controller\Backend;

use PHPUnit\Framework\TestCase;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagFuzzy\Tests\KernelTestCaseTrait;

require_once __DIR__ . '/../../../../Controllers/Backend/SwagFuzzyAnalytics.php';

class SwagFuzzyAnalyticsTest extends TestCase
{
    use DatabaseTransactionBehaviour;

    use KernelTestCaseTrait;

    public function test_getSearchesWithoutResultsAction_ensureCurrentCountForPeriod(): void
    {
        $sql = file_get_contents(__DIR__ . '/../../Components/_fixtures/search_term_statistic.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $view = new \Enlight_View_Default(Shopware()->Container()->get('template'));
        $request = new \Enlight_Controller_Request_RequestHttp();

        $request->setParam('searchTerm', 'foo');
        $request->setParam('fromDate', '1970-03-01T00:00:00');
        $request->setParam('toDate', '1970-06-30T23:59:59');

        $controller = $this->getController(null, $view, $request, null);

        $controller->getSearchesWithoutResultsAction();
        $result = $view->getAssign();

        static::assertTrue($result['success']);
        static::assertSame('1', $result['total']);
        static::assertSame('fooBar', $result['data'][0]['searchTerm']);
        static::assertSame('99', $result['data'][0]['searchesCount']);
        static::assertSame('4', $result['data'][0]['currentCount']);
    }

    public function test_getSearchesWithoutResultsAction_filterByDate(): void
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/install_fuzzy_statistics_with_no_result.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $view = new \Enlight_View_Default(Shopware()->Container()->get('template'));
        $request = new \Enlight_Controller_Request_RequestHttp();

        $formatTemplate = 'Y-m-d\TH:i:s';
        $fromDate = new \DateTime('1989-03-01 01:00:05');
        $toDate = new \DateTime('1989-09-01 00:00:00');

        $request->setParam('searchTerm', null);
        $request->setParam('fromDate', $fromDate->format($formatTemplate));
        $request->setParam('toDate', $toDate->format($formatTemplate));

        $controller = $this->getController(null, $view, $request, null);

        $controller->getSearchesWithoutResultsAction();
        $result = $view->getAssign();

        $sql = 'SELECT searchTerm, firstSearchDate FROM s_plugin_swag_fuzzy_statistics';
        $firstSearchDateResult = Shopware()->Container()->get('dbal_connection')->fetchAll($sql);

        static::assertTrue($result['success']);
        static::assertCount(2, $result['data']);

        foreach ($firstSearchDateResult as $firstSearchDateItem) {
            foreach ($result['data'] as $resultItem) {
                if ($resultItem['searchTerm'] === $firstSearchDateItem['searchTerm']) {
                    $lastSearchDate = new \DateTime($resultItem['lastSearchDate']);
                    $firstSearchDate = new \DateTime($firstSearchDateItem['firstSearchDate']);

                    static::assertLessThan($fromDate, $firstSearchDate);
                    static::assertGreaterThan($toDate, $lastSearchDate);

                    if ($fromDate < $firstSearchDate || $lastSearchDate < $toDate) {
                        static::fail(
                            sprintf(
                                'The search period from: %s to: %s is not between the "firstSearchDate" %s and "lastSearchDate" %s.',
                                $fromDate->format($formatTemplate),
                                $toDate->format($formatTemplate),
                                $firstSearchDate->format($formatTemplate),
                                $lastSearchDate->format($formatTemplate)
                            )
                        );
                    }
                }
            }
        }
    }

    public function test_getSearchesWithoutResultsAction_ensure_ToDate_IsAtTheEndOfTheDay()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/install_fuzzy_statistic_for_one_day.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $view = new \Enlight_View_Default(Shopware()->Container()->get('template'));
        $request = new \Enlight_Controller_Request_RequestHttp();

        $formatTemplate = 'Y-m-d\TH:i:s';
        $fromDate = new \DateTime('1980-01-03 00:00:00');
        $toDate = new \DateTime('1980-01-03 00:00:00'); // must be changed to 1980-01-03 23:59:59 in the controller

        $request->setParam('searchTerm', null);
        $request->setParam('fromDate', $fromDate->format($formatTemplate));
        $request->setParam('toDate', $toDate->format($formatTemplate));

        $controller = $this->getController(null, $view, $request, null);

        $controller->getSearchesWithoutResultsAction();
        $result = $view->getAssign();

        static::assertCount(2, $result['data']);

        $resultGER = $result['data'][0];
        $resultENG = $result['data'][1];

        static::assertEquals(5, $resultGER['searchesCount']);
        static::assertEquals(3, $resultGER['currentCount']);

        static::assertEquals(3, $resultENG['searchesCount']);
        static::assertEquals(2, $resultENG['currentCount']);
    }

    public function test_getSearchesWithoutResultsAction_shopFilter()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/install_fuzzy_statistic_for_one_day.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $shopID = 2; // shop en_GB

        $view = new \Enlight_View_Default(Shopware()->Container()->get('template'));
        $request = new \Enlight_Controller_Request_RequestHttp();

        $formatTemplate = 'Y-m-d\TH:i:s';
        $fromDate = new \DateTime('1980-01-03 00:00:00');
        $toDate = new \DateTime('1980-01-03 00:00:00');

        $request->setParam('searchTerm', null);
        $request->setParam('fromDate', $fromDate->format($formatTemplate));
        $request->setParam('toDate', $toDate->format($formatTemplate));
        $request->setParam('selectedShops', sprintf('%s', $shopID));

        $controller = $this->getController(null, $view, $request, null);

        $controller->getSearchesWithoutResultsAction();
        $result = $view->getAssign();

        static::assertCount(1, $result['data']);

        static::assertEquals(3, $result['data'][0]['searchesCount']);
        static::assertEquals(2, $result['data'][0]['currentCount']);
        static::assertEquals($shopID, $result['data'][0]['shopId']);
    }

    private function getController(
        \Enlight_Class $front = null,
        \Enlight_View_Default $view = null,
        \Enlight_Controller_Request_RequestHttp $request = null,
        \Enlight_Controller_Response_ResponseHttp $response = null
    ): \Shopware_Controllers_Backend_SwagFuzzyAnalytics {
        $controller = new \Shopware_Controllers_Backend_SwagFuzzyAnalytics();
        $controller->setContainer(Shopware()->Container());

        if ($front === null) {
            $front = \Enlight_Class::Instance(\Enlight_Controller_Front::class, [Shopware()->Container()->get('events')]);
        }

        if ($view === null) {
            $view = new \Enlight_View_Default(Shopware()->Container()->get('template'));
        }

        if ($request === null) {
            $request = new \Enlight_Controller_Request_RequestHttp();
        }

        if ($response === null) {
            $response = new \Enlight_Controller_Response_ResponseHttp();
        }

        $controller->setFront($front);
        $controller->setView($view);
        $controller->setRequest($request);
        $controller->setResponse($response);

        return $controller;
    }
}
