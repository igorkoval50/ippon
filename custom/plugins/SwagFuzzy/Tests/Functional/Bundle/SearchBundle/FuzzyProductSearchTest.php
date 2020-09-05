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

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\SearchBundle\ProductSearchResult;
use Shopware\Bundle\SearchBundle\StoreFrontCriteriaFactory;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class FuzzyProductSearchTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_search()
    {
        /** @var ContextService $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $context = $contextService->createShopContext(1);

        /** @var StoreFrontCriteriaFactory $criteriaService */
        $criteriaService = Shopware()->Container()->get('shopware_search.store_front_criteria_factory');
        $criteria = $criteriaService->createBaseCriteria([11], $context);

        $productSearch = Shopware()->Container()->get('shopware_search.product_search');
        /** @var ProductSearchResult $result */
        $result = $productSearch->search(
            $criteria,
            $context
        );

        $this->assertInstanceOf(ProductSearchResult::class, $result);
        $this->assertCount(23, $result->getProducts());
        $this->assertSame(23, $result->getTotalCount());
    }
}
