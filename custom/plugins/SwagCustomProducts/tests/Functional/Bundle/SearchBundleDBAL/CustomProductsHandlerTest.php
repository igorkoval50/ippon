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

namespace SwagCustomProducts\tests\Functional\Bundle\SearchBundleDBAL;

use Shopware\Bundle\SearchBundle\Condition\CategoryCondition;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResult\BooleanFacetResult;
use Shopware\Bundle\SearchBundle\Sorting\PriceSorting;
use Shopware\Bundle\SearchBundle\Sorting\ProductNameSorting;
use Shopware\Bundle\SearchBundle\SortingInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilderFactory;
use SwagCustomProducts\Bundle\SearchBundle\Condition\CustomProductsCondition;
use SwagCustomProducts\Bundle\SearchBundle\Facet\CustomProductsFacet;
use SwagCustomProducts\Bundle\SearchBundle\Sorting\CustomProductsSorting;
use SwagCustomProducts\Bundle\SearchBundleDBAL\CustomProductsHandler;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class CustomProductsHandlerTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_generateSorting_should_add_join_to_querybuilder()
    {
        $sorting = new ProductNameSorting();
        /** @var QueryBuilderFactory $queryBuilderFactory */
        $queryBuilderFactory = Shopware()->Container()->get('shopware_searchdbal.dbal_query_builder_factory');
        $queryBuilder = $queryBuilderFactory->createQueryBuilder();
        $shopContext = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();

        $queryBuilder->select('foo')
            ->from('test', 'product');

        $handler = $this->getHandler();
        $handler->generateSorting(
            $sorting,
            $queryBuilder,
            $shopContext
        );

        static::assertStringContainsString(
            'LEFT JOIN s_plugin_custom_products_template_product_relation customProduct ON customProduct.article_id = product.id',
            $queryBuilder->getSQL()
        );
        static::assertStringContainsString('customProduct.article_id IS NOT NULL', $queryBuilder->getSQL());
    }

    public function test_generateCondition_should_add_join_and_select_to_query()
    {
        $condition = new CategoryCondition([3]);
        /** @var QueryBuilderFactory $queryBuilderFactory */
        $queryBuilderFactory = Shopware()->Container()->get('shopware_searchdbal.dbal_query_builder_factory');
        $queryBuilder = $queryBuilderFactory->createQueryBuilder();
        $shopContext = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();

        $queryBuilder->select('foo')
            ->from('test', 'product');

        $handler = $this->getHandler();
        $handler->generateCondition(
            $condition,
            $queryBuilder,
            $shopContext
        );

        static::assertStringContainsString('SELECT product.id', $queryBuilder->getSQL());
        static::assertStringContainsString(
            'INNER JOIN s_plugin_custom_products_template_product_relation customProductRelation ON customProductRelation.article_id = product.id',
            $queryBuilder->getSQL()
        );
    }

    public function test_generateCondition_should_not_add_query_parts_query_already_executed()
    {
        $condition = new CategoryCondition([3]);
        /** @var QueryBuilderFactory $queryBuilderFactory */
        $queryBuilderFactory = Shopware()->Container()->get('shopware_searchdbal.dbal_query_builder_factory');
        $queryBuilder = $queryBuilderFactory->createQueryBuilder();
        $shopContext = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();

        $queryBuilder->select('foo')
            ->from('test', 'product');

        $queryBuilder->addState(CustomProductsHandler::CUSTOM_PRODUCTS_TABLE_JOINED);

        $handler = $this->getHandler();
        $handler->generateCondition(
            $condition,
            $queryBuilder,
            $shopContext
        );

        static::assertStringNotContainsString('s_plugin_custom_products_template_product_relation', $queryBuilder->getSQL());
    }

    public function test_supportsSorting_should_be_false()
    {
        $handler = $this->getHandler();

        $result = $handler->supportsSorting(new NotSupported_SortingMock());

        static::assertFalse($result);
    }

    public function test_supportsSorting_should_be_true()
    {
        $handler = $this->getHandler();

        $result = $handler->supportsSorting(new CustomProductsSorting());

        static::assertTrue($result);
    }

    public function test_supportsFacets_should_be_false()
    {
        $handler = $this->getHandler();

        $result = $handler->supportsFacet(new NotSupported_FaceMock());

        static::assertFalse($result);
    }

    public function test_supportsFacets_should_be_true()
    {
        $handler = $this->getHandler();

        $result = $handler->supportsFacet(new CustomProductsFacet());

        static::assertTrue($result);
    }

    public function test_supportsCondition_should_be_false()
    {
        $handler = $this->getHandler();

        $result = $handler->supportsCondition(new NotSupported_ConditionMock());

        static::assertFalse($result);
    }

    public function test_supportsCondition_should_be_true()
    {
        $handler = $this->getHandler();

        $result = $handler->supportsCondition(new CustomProductsCondition());

        static::assertTrue($result);
    }

    public function test_generateSorting()
    {
        $handler = $this->getHandler();

        $sorting = new PriceSorting();
        $queryBuilder = new QueryBuilder(Shopware()->Container()->get('dbal_connection'));
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $queryBuilder->addSelect('*')
            ->from('s_articles', 'product');

        $expectedResult = 'SELECT * FROM s_articles product LEFT JOIN s_plugin_custom_products_template_product_relation customProduct ON customProduct.article_id = product.id ORDER BY customProduct.article_id IS NOT NULL ASC';

        $handler->generateSorting($sorting, $queryBuilder, $context);

        static::assertSame($expectedResult, $queryBuilder->getSQL());
    }

    public function test_generateFacet_should_be_false()
    {
        $handler = $this->getHandler();
        $facet = new CustomProductsFacet();
        $criteria = new Criteria();
        $condition = new CategoryCondition([11]); // 11 Categories Tees
        $criteria->addCondition($condition);
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $result = $handler->generateFacet($facet, $criteria, $context);

        static::assertFalse($result);
    }

    public function test_generateFacet()
    {
        $handler = $this->getHandler();
        $this->installCustomProduct();
        $facet = new CustomProductsFacet();
        $criteria = new Criteria();
        $condition = new CategoryCondition([11]); // 11 Categories Tees
        $criteria->addCondition($condition);
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $result = $handler->generateFacet($facet, $criteria, $context);

        static::assertInstanceOf(BooleanFacetResult::class, $result);
    }

    public function test_generatePartialFacet_should_be_false()
    {
        $handler = $this->getHandler();
        $facet = new CustomProductsFacet();
        $criteria = new Criteria();
        $criteriaRev = new Criteria();
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $result = $handler->generatePartialFacet($facet, $criteriaRev, $criteria, $context);

        static::assertFalse($result);
    }

    public function test_generatePartialFacet()
    {
        $handler = $this->getHandler();
        $this->installCustomProduct();
        $facet = new CustomProductsFacet();
        $criteria = new Criteria();
        $criteriaRev = new Criteria();
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $result = $handler->generatePartialFacet($facet, $criteriaRev, $criteria, $context);

        static::assertInstanceOf(BooleanFacetResult::class, $result);
    }

    public function test_generateCondition()
    {
        $handler = $this->getHandler();

        $condition = new CustomProductsCondition();
        $queryBuilder = new QueryBuilder(Shopware()->Container()->get('dbal_connection'));
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $queryBuilder->addSelect('*')
            ->from('s_articles', 'product');

        $handler->generateCondition($condition, $queryBuilder, $context);

        $expectedResult = 'SELECT product.id FROM s_articles product INNER JOIN s_plugin_custom_products_template_product_relation customProductRelation ON customProductRelation.article_id = product.id';

        static::assertSame($expectedResult, $queryBuilder->getSQL());
    }

    public function test_generateCondition_has_state()
    {
        $handler = $this->getHandler();

        $condition = new CustomProductsCondition();
        $queryBuilder = new QueryBuilder(Shopware()->Container()->get('dbal_connection'));
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $queryBuilder->addSelect('*')
            ->from('s_articles', 'product');

        $queryBuilder->addState('s_plugin_custom_products_template_product_relation');

        $handler->generateCondition($condition, $queryBuilder, $context);

        $expectedResult = 'SELECT * FROM s_articles product';

        static::assertSame($expectedResult, $queryBuilder->getSQL());
    }

    private function installCustomProduct()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/custom_product.sql');
        $this->execSql($sql);
    }

    private function getHandler()
    {
        return new CustomProductsHandler(
            Shopware()->Container()
        );
    }
}

class NotSupported_SortingMock implements SortingInterface
{
    public function getName()
    {
        return 'notSupported';
    }
}

class NotSupported_FaceMock implements FacetInterface
{
    public function getName()
    {
        return 'notSupported';
    }
}

class NotSupported_ConditionMock implements ConditionInterface
{
    public function getName()
    {
        return 'notSupported';
    }
}
