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

namespace SwagCustomProducts\Bundle\SearchBundleDBAL;

use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResult\BooleanFacetResult;
use Shopware\Bundle\SearchBundle\SortingInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\FacetHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\PartialFacetHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\SearchBundleDBAL\SortingHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use SwagCustomProducts\Bundle\SearchBundle\Condition\CustomProductsCondition;
use SwagCustomProducts\Bundle\SearchBundle\Facet\CustomProductsFacet;
use SwagCustomProducts\Bundle\SearchBundle\Sorting\CustomProductsSorting;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomProductsHandler implements SortingHandlerInterface, FacetHandlerInterface, PartialFacetHandlerInterface, ConditionHandlerInterface
{
    const CUSTOM_PRODUCTS_TABLE_JOINED = 's_plugin_custom_products_template_product_relation';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsSorting(SortingInterface $sorting)
    {
        return $sorting instanceof CustomProductsSorting;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsFacet(FacetInterface $facet)
    {
        return $facet instanceof CustomProductsFacet;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        return $condition instanceof CustomProductsCondition;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSorting(
        SortingInterface $sorting,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
        $this->addJoin($query);

        /* @var CustomProductsSorting $sorting */
        $query->addOrderBy('customProduct.article_id IS NOT NULL', $sorting->getDirection());
    }

    /**
     * {@inheritdoc}
     */
    public function generateFacet(
        FacetInterface $facet,
        Criteria $criteria,
        Struct\ShopContextInterface $context
    ) {
        $reverted = clone $criteria;
        $reverted->resetConditions();
        $reverted->resetSorting();

        return $this->generatePartialFacet($facet, $reverted, $criteria, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function generatePartialFacet(
        FacetInterface $facet,
        Criteria $reverted,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        /** @var \Shopware\Bundle\SearchBundleDBAL\QueryBuilderFactoryInterface $factory */
        $factory = $this->container->get('shopware_searchdbal.dbal_query_builder_factory');

        $query = $factory->createQuery($reverted, $context);

        $query->select(['product.id']);
        $query->setMaxResults(1);

        $this->addJoin($query);

        $query->andWhere('customProduct.article_id IS NOT NULL');

        /** @var \PDOStatement $statement */
        $statement = $query->execute();

        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($data)) {
            return false;
        }

        /** @var CustomProductsFacet $facet */
        $label = $facet->getLabel();

        if ($label === null) {
            $label = $this->container->get('snippets')
                ->getNamespace('frontend/listing/index')
                ->get('FacetName');
        }

        return new BooleanFacetResult(
            $facet->getName(),
            'custom_products',
            $criteria->hasCondition($facet->getName()),
            $label
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateCondition(
        ConditionInterface $condition,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
        if ($query->hasState(self::CUSTOM_PRODUCTS_TABLE_JOINED)) {
            return;
        }
        $query->addState(self::CUSTOM_PRODUCTS_TABLE_JOINED);

        $query->select(['product.id']);
        $query->innerJoin(
            'product',
            's_plugin_custom_products_template_product_relation',
            'customProductRelation',
            'customProductRelation.article_id = product.id'
        );
    }

    private function addJoin(QueryBuilder $query)
    {
        $query->leftJoin(
            'product',
            's_plugin_custom_products_template_product_relation',
            'customProduct',
            'customProduct.article_id = product.id'
        );
    }
}
