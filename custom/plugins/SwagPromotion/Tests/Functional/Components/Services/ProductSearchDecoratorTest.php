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

namespace SwagPromotion\Tests\Functional\Components\Services;

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\SearchBundle\Condition\CategoryCondition;
use Shopware\Bundle\SearchBundle\Condition\CustomerGroupCondition;
use Shopware\Bundle\SearchBundle\Condition\PriceCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use SwagPromotion\Components\Services\ProductSearchDecorator;

class ProductSearchDecoratorTest extends TestCase
{
    public function test_search_considerLowerPromotionPrices()
    {
        $service = $this->getProductsearchDecorator();

        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1, 1, 'EK');

        $criteria = new Criteria();
        $criteria->addCondition(new CustomerGroupCondition([1]));
        $criteria->addCondition(new CategoryCondition([14]));

        $minPrice = 15;
        $criteria->addCondition(new PriceCondition($minPrice, 50));

        $result = $service->search($criteria, $context);

        foreach ($result->getProducts() as $product) {
            if ($product->getCheapestPrice()->getCalculatedPrice() < $minPrice) {
                static::fail('The calculated price is to low');
            }
        }

        static::assertSame(5, $result->getTotalCount());
    }

    private function getProductsearchDecorator()
    {
        return new ProductSearchDecorator(
            Shopware()->Container()->get('shopware_search.product_search'),
            Shopware()->Container()->get('config'),
            Shopware()->Front()
        );
    }
}
