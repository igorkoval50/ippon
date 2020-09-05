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
use Shopware\Bundle\SearchBundle\Facet\CategoryFacet;
use Shopware\Bundle\SearchBundle\FacetResult\FacetResultGroup;
use Shopware\Bundle\SearchBundle\StoreFrontCriteriaFactory;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use SwagFuzzy\Bundle\SearchBundle\Facet\KeywordFacet;
use SwagFuzzy\Bundle\SearchBundleDBAL\FacetHandler\KeywordFacetHandler;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class KeywordFacetHandlerTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_supportsFacet_should_be_false()
    {
        $handler = $this->getHandler();
        $facet = new CategoryFacet();

        $result = $handler->supportsFacet($facet);

        $this->assertFalse($result);
    }

    public function test_supportsFacet_should_be_true()
    {
        $handler = $this->getHandler();
        $facet = new KeywordFacet('term');

        $result = $handler->supportsFacet($facet);

        $this->assertTrue($result);
    }

    public function test_generatePartialFacet()
    {
        $handler = $this->getHandler();

        $request = new Enlight_Controller_Request_RequestHttp();
        $request->setParam('sSearch', 'term');

        /** @var ContextService $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $context = $contextService->createShopContext(1);

        /** @var StoreFrontCriteriaFactory $criteriaService */
        $criteriaService = Shopware()->Container()->get('shopware_search.store_front_criteria_factory');
        $criteria = $criteriaService->createSearchCriteria($request, $context);

        $revCriteria = $criteriaService->createBaseCriteria([11], $context);
        $revCriteria->resetBaseConditions();
        $revCriteria->resetConditions();
        $revCriteria->resetFacets();
        $revCriteria->resetSorting();

        $facet = new KeywordFacet('term');

        $result = $handler->generatePartialFacet($facet, $revCriteria, $criteria, $context);

        $this->assertInstanceOf(FacetResultGroup::class, $result);
    }

    /**
     * @return KeywordFacetHandler
     */
    private function getHandler()
    {
        return new KeywordFacetHandler(
            Shopware()->Container()->get('shopware_searchdbal.keyword_finder_dbal'),
            Shopware()->Container()->get('shopware_searchdbal.search_term_helper'),
            Shopware()->Container()->get('swag_fuzzy.similar_results_service'),
            Shopware()->Container()->get('swag_fuzzy.synonym_service'),
            Shopware()->Container()->get('snippets')
        );
    }
}
