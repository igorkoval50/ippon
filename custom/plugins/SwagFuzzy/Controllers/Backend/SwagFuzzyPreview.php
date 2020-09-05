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

use Shopware\Bundle\SearchBundle\Condition\CategoryCondition;
use Shopware\Bundle\SearchBundle\Condition\CustomerGroupCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\ProductSearchResult;
use Shopware\Bundle\SearchBundle\Sorting\SearchRankingSorting;
use Shopware\Bundle\SearchBundle\SortingInterface;
use Shopware\Bundle\SearchBundleDBAL\KeywordFinderInterface;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\Keyword;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContext;
use SwagFuzzy\Bundle\SearchBundle\Condition\DebugSearchTermCondition;

/**
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzyPreview extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * Assigns the search list to the view
     */
    public function listAction()
    {
        $filter = $this->Request()->getParam('filter', []);
        $search = '';
        foreach ($filter as $expression) {
            if ($expression['property'] == 'search') {
                $search = $expression['value'];
            }
        }

        $list = $this->getList(
            $search,
            $this->Request()->getParam('shopId', 1),
            $this->Request()->getParam('start', 0),
            $this->Request()->getParam('limit', 20)
        );

        $list['data'] = array_map(function ($product) {
            $data = json_decode(json_encode($product), true);
            unset($data['attributes']['core']);

            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'number' => $data['number'],
                'attributes' => $data['attributes'],
            ];
        }, $list['data']);

        $this->View()->assign(
            $list
        );
    }

    /**
     * @param string $search
     * @param int    $shopId
     * @param int    $offset
     * @param int    $limit
     *
     * @return array
     */
    private function getList($search, $shopId, $offset, $limit)
    {
        $criteria = new Criteria();

        $criteria->offset($offset);
        $criteria->limit($limit);

        $context = $this->createContext($shopId);
        if (!$context) {
            return [
                'success' => true,
                'data' => [],
                'total' => 0,
            ];
        }

        $customerGroupId = $context->getCurrentCustomerGroup()->getId();
        $criteria->addBaseCondition(new CustomerGroupCondition([$customerGroupId]));

        $categoryId = $context->getShop()->getCategory()->getId();
        $criteria->addBaseCondition(new CategoryCondition([$categoryId]));

        $criteria->addBaseCondition(new DebugSearchTermCondition($search));

        $criteria->addSorting(new SearchRankingSorting(SortingInterface::SORT_DESC));

        /** @var $result ProductSearchResult */
        $result = $this->get('shopware_search.product_search')->search($criteria, $context);

        $products = array_values($result->getProducts());

        $keywords = [];
        if (!empty($search)) {
            $keywords = $this->getKeywords($search);
        }

        return [
            'success' => true,
            'data' => $products,
            'keywords' => $keywords,
            'total' => $result->getTotalCount(),
        ];
    }

    /**
     * @param string $search
     *
     * @return array
     */
    private function getKeywords($search)
    {
        /** @var KeywordFinderInterface $keywordFinder */
        $keywordFinder = $this->get('shopware_searchdbal.keyword_finder_dbal');
        $keywords = $keywordFinder->getKeywordsOfTerm($search);

        $keywords = array_map(function ($keyword) {
            /* @var Keyword $keyword */
            return [
                'id' => $keyword->getId(),
                'term' => $keyword->getTerm(),
                'word' => $keyword->getWord(),
                'relevance' => $keyword->getRelevance(),
            ];
        }, $keywords);

        return $keywords;
    }

    /**
     * @param int $shopId
     *
     * @return ProductContext|null
     */
    private function createContext($shopId)
    {
        /** @var Shopware\Models\Shop\Repository $repo */
        $repo = $this->get('models')->getRepository(\Shopware\Models\Shop\Shop::class);
        $shop = $repo->getActiveById($shopId);

        if (!$shop) {
            return null;
        }

        $shop->registerResources($this->get('bootstrap'));

        return $this->get('shopware_storefront.context_service')->getProductContext();
    }
}
