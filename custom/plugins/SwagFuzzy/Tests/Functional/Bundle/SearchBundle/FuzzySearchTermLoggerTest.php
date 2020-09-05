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

namespace SwagFuzzy\Tests\Functional\Bundle\SearchBundle;

use Enlight_Controller_Request_RequestHttp;
use PHPUnit\Framework\TestCase;
use Shopware\Bundle\SearchBundle\ProductSearchResult;
use Shopware\Bundle\SearchBundle\StoreFrontCriteriaFactory;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class FuzzySearchTermLoggerTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_logResult()
    {
        $logger = Shopware()->Container()->get('shopware_searchdbal.search_term_logger');

        $searchTerm = 'ibiza';

        $request = new Enlight_Controller_Request_RequestHttp();
        $request->setParam('sSearch', $searchTerm);

        /** @var ContextService $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $context = $contextService->createShopContext(1);

        /** @var StoreFrontCriteriaFactory $criteriaService */
        $criteriaService = Shopware()->Container()->get('shopware_search.store_front_criteria_factory');
        $criteria = $criteriaService->createSearchCriteria($request, $context);

        $productSearch = Shopware()->Container()->get('shopware_search.product_search');
        /** @var ProductSearchResult $result */
        $searchResult = $productSearch->search(
            $criteria,
            $context
        );

        $shop = new Shop();
        $shop->setId(1);

        $logger->logResult(
            $criteria,
            $searchResult,
            $shop
        );

        $sql = 'SELECT * FROM s_plugin_swag_fuzzy_statistics WHERE searchTerm = :searchTerm';
        $result = Shopware()->Container()->get('dbal_connection')->fetchAssoc($sql, [':searchTerm' => $searchTerm]);

        $expectedResult = [
            'shopId' => '1',
            'searchTerm' => 'ibiza',
            'searchesCount' => '1',
            'resultsCount' => '1',
        ];

        $this->assertArraySubset($expectedResult, $result);
    }
}
