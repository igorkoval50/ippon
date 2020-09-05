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
use Shopware\Bundle\SearchBundle\Condition\SearchTermCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use SwagFuzzy\Bundle\SearchBundle\CriteriaRequestHandler\CriteriaRequestHandler;
use SwagFuzzy\Bundle\SearchBundle\Facet\KeywordFacet;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class CriteriaRequestHandlerTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_handleRequest_has_no_condition()
    {
        $requestHandler = new CriteriaRequestHandler();
        $request = new Enlight_Controller_Request_RequestHttp();
        $criteria = new Criteria();

        /** @var ContextService $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $shopContext = $contextService->createShopContext(1);

        $requestHandler->handleRequest($request, $criteria, $shopContext);
        $result = $criteria->getFacets();

        $this->assertCount(0, $result);
    }

    public function test_handleRequest_should_have_condition()
    {
        $requestHandler = new CriteriaRequestHandler();
        $request = new Enlight_Controller_Request_RequestHttp();
        $criteria = new Criteria();
        $criteria->addCondition(new SearchTermCondition('test search term'));

        /** @var ContextService $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $shopContext = $contextService->createShopContext(1);

        $requestHandler->handleRequest($request, $criteria, $shopContext);
        $result = $criteria->getFacets();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(KeywordFacet::class, $result['keyword_facet']);
    }
}
