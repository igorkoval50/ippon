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

namespace Shopware\SwagPromotion\Tests;

use PHPUnit\Framework\TestCase;
use SwagPromotion\Components\DataProvider\CustomerDataProvider;
use SwagPromotion\Components\DataProvider\ProductDataProvider;

/**
 * @small
 */
class DataProviderTest extends TestCase
{
    /**
     * @var array
     */
    protected static $ensureLoadedPlugins = [
        'SwagPromotion' => [],
    ];

    public function testCustomerDataProvider()
    {
        $dataProvider = new CustomerDataProvider(Shopware()->Db());
        $customer = $dataProvider->get(1);

        $expectedResultSubset = [
            'user::id' => '1',
            'user::email' => 'test@example.com',
            'user::active' => '1',
            'user::accountmode' => '0',
            'user::paymentID' => '5',
            'user::customergroup' => 'EK',
        ];

        static::assertSame($expectedResultSubset['user::id'], $customer['user::id']);
        static::assertSame($expectedResultSubset['user::email'], $customer['user::email']);
        static::assertSame($expectedResultSubset['user::active'], $customer['user::active']);
        static::assertSame($expectedResultSubset['user::accountmode'], $customer['user::accountmode']);
        static::assertSame($expectedResultSubset['user::paymentID'], $customer['user::paymentID']);
        static::assertSame($expectedResultSubset['user::customergroup'], $customer['user::customergroup']);
    }

    public function testProductDataProvider()
    {
        $dataProvider = new ProductDataProvider(
            Shopware()->Db(),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        // Not existing article ordernumbers in Basket
        $products = $dataProvider->get([1, 2]);
        static::assertEmpty($products);

        // Existing article ordernumbers in Basket
        $products = ['SW10009' => 4, 'SW10010' => 1];
        Shopware()->Modules()->Basket()->sDeleteBasket();
        foreach ($products as $number => $quantity) {
            Shopware()->Modules()->Basket()->sAddArticle($number, $quantity);
        }

        $basketProducts = $dataProvider->get(['SW10009' => 4, 'SW10010' => 1]);
        static::assertNotEmpty($basketProducts);
    }
}
