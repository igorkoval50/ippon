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

use Enlight_Controller_Request_Request as Request;
use Shopware\Bundle\SearchBundle\StoreFrontCriteriaFactoryInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use SwagFuzzy\Bundle\SearchBundle\Facet\KeywordFacet;

/**
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class FuzzyStoreFrontCriteriaFactory implements StoreFrontCriteriaFactoryInterface
{
    /**
     * @var StoreFrontCriteriaFactoryInterface
     */
    private $storeFrontCriteriaFactory;

    public function __construct(StoreFrontCriteriaFactoryInterface $storeFrontCriteriaFactory)
    {
        $this->storeFrontCriteriaFactory = $storeFrontCriteriaFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAjaxSearchCriteria(Request $request, ShopContextInterface $context)
    {
        $criteria = $this->storeFrontCriteriaFactory->createAjaxSearchCriteria($request, $context);

        $keywordFacet = new KeywordFacet($criteria->getCondition('search')->getTerm());
        $criteria->addFacet($keywordFacet);

        return $criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function createBaseCriteria($categoryIds, ShopContextInterface $context)
    {
        return $this->storeFrontCriteriaFactory->createBaseCriteria($categoryIds, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function createSearchCriteria(Request $request, ShopContextInterface $context)
    {
        return $this->storeFrontCriteriaFactory->createSearchCriteria($request, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function createListingCriteria(Request $request, ShopContextInterface $context)
    {
        return $this->storeFrontCriteriaFactory->createListingCriteria($request, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function createAjaxListingCriteria(Request $request, ShopContextInterface $context)
    {
        return $this->storeFrontCriteriaFactory->createAjaxListingCriteria($request, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function createAjaxCountCriteria(Request $request, ShopContextInterface $context)
    {
        return $this->storeFrontCriteriaFactory->createAjaxCountCriteria($request, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function createProductNavigationCriteria(Request $request, ShopContextInterface $context, $categoryId)
    {
        return $this->storeFrontCriteriaFactory->createProductNavigationCriteria($request, $context, $categoryId);
    }
}
