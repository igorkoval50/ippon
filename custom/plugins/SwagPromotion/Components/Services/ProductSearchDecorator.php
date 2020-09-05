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

namespace SwagPromotion\Components\Services;

use Enlight_Controller_Front;
use Shopware\Bundle\SearchBundle\Condition\PriceCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\ProductSearchInterface;
use Shopware\Bundle\SearchBundle\ProductSearchResult;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface;
use Shopware_Components_Config;

class ProductSearchDecorator implements ProductSearchInterface
{
    /**
     * @var ProductSearchInterface
     */
    private $productSearch;

    /**
     * @var Shopware_Components_Config
     */
    private $config;

    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    public function __construct(ProductSearchInterface $productSearch, Shopware_Components_Config $config, Enlight_Controller_Front $front)
    {
        $this->productSearch = $productSearch;
        $this->config = $config;
        $this->front = $front;
    }

    public function search(Criteria $criteria, ProductContextInterface $context)
    {
        $products = [];
        $priceCondition = null;
        foreach ($criteria->getConditions() as $condition) {
            if (!$condition instanceof PriceCondition) {
                continue;
            }

            $priceCondition = $condition;
        }

        if ($priceCondition === null) {
            return $this->productSearch->search($criteria, $context);
        }

        $criteria->limit($this->config->get('articlesperpage'));

        $loadProducts = $this->front->Request()->getParam('loadProducts');
        if ($loadProducts === null) {
            $this->front->Request()->setParam('loadProducts', true);
        }

        $result = $this->productSearch->search($criteria, $context);
        if ($loadProducts === null) {
            $this->front->Request()->setParam('loadProducts', false);
        }

        foreach ($result->getProducts() as $product) {
            if ((float) $priceCondition->getMinPrice() > (float) $product->getCheapestPrice()->getCalculatedPrice()) {
                continue;
            }

            $products[$product->getNumber()] = $product;
        }

        return new ProductSearchResult(
            $products,
            $result->getTotalCount(),
            $result->getFacets(),
            $criteria,
            $context
        );
    }
}
