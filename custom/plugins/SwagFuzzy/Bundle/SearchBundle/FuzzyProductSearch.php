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

namespace SwagFuzzy\Bundle\SearchBundle;

use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\ProductSearchInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

/**
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class FuzzyProductSearch implements ProductSearchInterface
{
    /**
     * @var ProductSearchInterface
     */
    private $productSearch;

    public function __construct(ProductSearchInterface $productSearch)
    {
        $this->productSearch = $productSearch;
    }

    /**
     * {@inheritdoc}
     */
    public function search(Criteria $criteria, Struct\ProductContextInterface $context)
    {
        $productSearchResult = $this->productSearch->search($criteria, $context);

        $facets = $productSearchResult->getFacets();

        if (!empty($facets) && $facets[0]->getFacetName() === 'keyword_facet') {
            Shopware()->Template()->assign('swagFuzzyFacets', $facets);
        }

        return $productSearchResult;
    }
}
