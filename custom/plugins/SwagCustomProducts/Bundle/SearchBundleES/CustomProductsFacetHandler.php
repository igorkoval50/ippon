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

namespace SwagCustomProducts\Bundle\SearchBundleES;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\FilterAggregation;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaPartInterface;
use Shopware\Bundle\SearchBundle\FacetResult\BooleanFacetResult;
use Shopware\Bundle\SearchBundle\ProductNumberSearchResult;
use Shopware\Bundle\SearchBundleES\HandlerInterface;
use Shopware\Bundle\SearchBundleES\ResultHydratorInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware_Components_Snippet_Manager;
use SwagCustomProducts\Bundle\SearchBundle\Facet\CustomProductsFacet;

class CustomProductsFacetHandler implements HandlerInterface, ResultHydratorInterface
{
    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippets;

    public function __construct(Shopware_Components_Snippet_Manager $snippets)
    {
        $this->snippets = $snippets;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(CriteriaPartInterface $criteriaPart)
    {
        return $criteriaPart instanceof CustomProductsFacet;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        CriteriaPartInterface $criteriaPart,
        Criteria $criteria,
        Search $search,
        ShopContextInterface $context
    ) {
        $query = new TermQuery('attributes.swag_custom_product.is_custom_product', 'true');

        $search->addAggregation(
            new FilterAggregation('custom_products', $query)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(
        array $elasticResult,
        ProductNumberSearchResult $result,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        if (!isset($elasticResult['aggregations'])) {
            return;
        }
        if (!isset($elasticResult['aggregations']['custom_products'])) {
            return;
        }

        $data = $elasticResult['aggregations']['custom_products'];
        if ((int) $data['doc_count'] === 0) {
            return;
        }

        /** @var CustomProductsFacet $facet */
        $facet = $criteria->getFacet('custom_products');
        $label = $facet->getLabel();

        if ($label === null) {
            $label = $this->snippets->getNamespace('frontend/listing/index')->get('FacetName');
        }

        $result->addFacet(
            new BooleanFacetResult(
                'custom_products',
                'custom_products',
                $criteria->hasCondition('custom_products'),
                $label
            )
        );
    }
}
