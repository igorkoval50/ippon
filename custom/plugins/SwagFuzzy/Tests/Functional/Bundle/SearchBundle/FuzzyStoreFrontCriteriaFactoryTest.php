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
use Shopware\Bundle\SearchBundle\Condition\CategoryCondition;
use Shopware\Bundle\SearchBundle\Condition\CustomerGroupCondition;
use Shopware\Bundle\SearchBundle\Condition\SearchTermCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\Facet\ImmediateDeliveryFacet;
use Shopware\Bundle\SearchBundle\Facet\ManufacturerFacet;
use Shopware\Bundle\SearchBundle\Facet\PriceFacet;
use Shopware\Bundle\SearchBundle\Facet\PropertyFacet;
use Shopware\Bundle\SearchBundle\Facet\ShippingFreeFacet;
use Shopware\Bundle\SearchBundle\Facet\VoteAverageFacet;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class FuzzyStoreFrontCriteriaFactoryTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_createAjaxSearchCriteria()
    {
        $factory = $this->getFactory();
        $request = $this->getSearchRequest();
        $context = $this->getShopContext();

        $result = $factory->createAjaxSearchCriteria($request, $context);
        $condition = $result->getCondition('search');

        $this->assertInstanceOf(Criteria::class, $result);
        $this->assertInstanceOf(SearchTermCondition::class, $condition);
        $this->assertSame('searchTerm', $condition->getTerm());
    }

    public function test_createBaseCriteria()
    {
        $factory = $this->getFactory();
        $context = $this->getShopContext();

        $result = $factory->createBaseCriteria([11], $context);
        /** @var CategoryCondition $condition */
        $condition = $result->getBaseCondition('category');

        $expectedResult = [
            0 => 11,
        ];

        $this->assertInstanceOf(Criteria::class, $result);
        $this->assertInstanceOf(CategoryCondition::class, $condition);
        $this->assertArraySubset($expectedResult, $condition->getCategoryIds());
    }

    public function test_createSearchCriteria()
    {
        $factory = $this->getFactory();
        $request = $this->getSearchRequest();
        $context = $this->getShopContext();

        $result = $factory->createSearchCriteria($request, $context);
        $condition = $result->getBaseCondition('search');

        $this->assertInstanceOf(Criteria::class, $result);
        $this->assertInstanceOf(SearchTermCondition::class, $condition);
        $this->assertSame('searchTerm', $condition->getTerm());
    }

    public function test_createListingCriteria()
    {
        $factory = $this->getFactory();
        $request = $this->getSearchRequest();
        $context = $this->getShopContext();

        $result = $factory->createListingCriteria($request, $context);

        $this->assertInstanceOf(Criteria::class, $result);

        $this->assertInstanceOf(CustomerGroupCondition::class, $result->getBaseCondition('customer_group'));
        $this->assertInstanceOf(SearchTermCondition::class, $result->getBaseCondition('search'));

        $this->assertInstanceOf(ImmediateDeliveryFacet::class, $result->getFacet('immediate_delivery'));
        $this->assertInstanceOf(ShippingFreeFacet::class, $result->getFacet('shipping_free'));
        $this->assertInstanceOf(PriceFacet::class, $result->getFacet('price'));
        $this->assertInstanceOf(VoteAverageFacet::class, $result->getFacet('vote_average'));
        $this->assertInstanceOf(ManufacturerFacet::class, $result->getFacet('manufacturer'));
        $this->assertInstanceOf(PropertyFacet::class, $result->getFacet('property'));
    }

    public function test_createAjaxListingCriteria()
    {
        $factory = $this->getFactory();
        $request = $this->getSearchRequest();
        $context = $this->getShopContext();

        $result = $factory->createAjaxListingCriteria($request, $context);

        $this->assertInstanceOf(Criteria::class, $result);

        $this->assertInstanceOf(CustomerGroupCondition::class, $result->getBaseCondition('customer_group'));
        $this->assertInstanceOf(SearchTermCondition::class, $result->getBaseCondition('search'));
    }

    public function test_createAjaxCountCriteria()
    {
        $factory = $this->getFactory();
        $request = $this->getSearchRequest();
        $context = $this->getShopContext();

        $result = $factory->createAjaxCountCriteria($request, $context);

        $this->assertInstanceOf(Criteria::class, $result);

        $this->assertInstanceOf(CustomerGroupCondition::class, $result->getBaseCondition('customer_group'));
        $this->assertInstanceOf(SearchTermCondition::class, $result->getBaseCondition('search'));
    }

    public function test_createProductNavigationCriteria()
    {
        $factory = $this->getFactory();
        $request = $this->getSearchRequest();
        $context = $this->getShopContext();

        $result = $factory->createProductNavigationCriteria($request, $context, 11);

        $this->assertInstanceOf(Criteria::class, $result);

        $this->assertInstanceOf(CustomerGroupCondition::class, $result->getBaseCondition('customer_group'));
        $this->assertInstanceOf(SearchTermCondition::class, $result->getBaseCondition('search'));
        $this->assertInstanceOf(CategoryCondition::class, $result->getBaseCondition('category'));
    }

    private function getFactory()
    {
        return Shopware()->Container()->get('shopware_search.store_front_criteria_factory');
    }

    private function getSearchRequest()
    {
        $request = new Enlight_Controller_Request_RequestHttp();
        $request->setParam('sSearch', 'searchTerm');

        return $request;
    }

    private function getShopContext()
    {
        /** @var ContextService $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');

        return $contextService->createShopContext(1);
    }
}
