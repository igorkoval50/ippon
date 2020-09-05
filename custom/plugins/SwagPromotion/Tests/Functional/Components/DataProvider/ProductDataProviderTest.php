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
use SwagPromotion\Components\DataProvider\ProductDataProvider;
use SwagPromotion\Tests\DatabaseTestCaseTrait;

class ProductDataProviderTest extends TestCase
{
    use DatabaseTestCaseTrait;

    /**
     * @after
     */
    public function afterTestResetContextService(): void
    {
        Shopware()->Session()->unsetAll();
        Shopware()->Container()->get('shopware_storefront.context_service')->initializeShopContext();
    }

    public function test_apply_pseudo_price_for_customer_group(): void
    {
        $productDataProvider = $this->getProductDataProvider();
        $product1 = 'SW10178'; // Strandtuch Ibiza
        $product2 = 'SW10239'; // Spachtelmasse
        $customerGroupKey = 'FB';

        // create new customerGroup
        Shopware()->Container()->get('dbal_connection')->exec(file_get_contents(__DIR__ . '/_fixtures/customer_group.sql'));
        // create new user
        Shopware()->Container()->get('dbal_connection')->exec(file_get_contents(__DIR__ . '/_fixtures/user.sql'));
        // and login
        Shopware()->Session()->unsetAll();
        Shopware()->Session()->offsetSet('sessionId', 'sessionId1');
        Shopware()->Session()->offsetSet('sUserId', 300);
        Shopware()->Session()->offsetSet('sUserPassword', 'phpUnitTestPassword');
        Shopware()->Session()->offsetSet('sUserMail', 'test@unit.com');
        Shopware()->Session()->offsetSet('sUserGroup', $customerGroupKey);
        Shopware()->Container()->get('shopware_storefront.context_service')->initializeShopContext();

        // check user is logged in
        $usUserLoggedIn = Shopware()->Modules()->Admin()->sCheckUser();
        static::assertTrue($usUserLoggedIn, 'No user is logged in.');

        // check customer group key
        $customerGroupKeyResult = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext()->getCurrentCustomerGroup()->getKey();
        static::assertSame($customerGroupKey, $customerGroupKeyResult, 'CustomerGroup is wrong');

        // create customer group specific pseudo price for Strandtuch Ibiza
        Shopware()->Container()->get('dbal_connection')->exec(file_get_contents(__DIR__ . '/_fixtures/pseudo_price.sql'));

        Shopware()->Modules()->Basket()->sDeleteBasket();
        Shopware()->Modules()->Basket()->sAddArticle($product1, 1);
        Shopware()->Modules()->Basket()->sAddArticle($product2, 1);

        $result = $productDataProvider->get([$product1 => 1, $product2 => 1]);

        static::assertSame($result[$product1]['price::pseudoprice'], '84.025210084034');
    }

    public function test_do_not_apply_pseudo_price(): void
    {
        $productDataProvider = $this->getProductDataProvider();
        $product1 = 'SW10178'; // Strandtuch Ibiza
        $product2 = 'SW10239'; // Spachtelmasse
        $customerGroupKey = 'EK';

        // create new customerGroup
        Shopware()->Container()->get('dbal_connection')->exec(file_get_contents(__DIR__ . '/_fixtures/customer_group.sql'));
        // update default user
        Shopware()->Container()->get('dbal_connection')->exec(file_get_contents(__DIR__ . '/_fixtures/update_default_user.sql'));
        // and login as Max Mustermann,
        Shopware()->Session()->unsetAll();
        Shopware()->Session()->offsetSet('sessionId', 'sessionId2');
        Shopware()->Session()->offsetSet('sUserId', 1);
        Shopware()->Session()->offsetSet('sUserPassword', 'testPassword123');
        Shopware()->Session()->offsetSet('sUserMail', 'test@example.com');
        Shopware()->Session()->offsetSet('sUserGroup', 'EK');
        Shopware()->Container()->get('shopware_storefront.context_service')->initializeShopContext();

        // check user is logged in
        $usUserLoggedIn = Shopware()->Modules()->Admin()->sCheckUser();
        static::assertTrue($usUserLoggedIn, 'No user is logged in.');

        // check customer group key
        $customerGroupKeyResult = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext()->getCurrentCustomerGroup()->getKey();
        static::assertSame($customerGroupKey, $customerGroupKeyResult, 'CustomerGroup is wrong');

        // create pseudo price for FooBar (FB) customer group for Strandtuch Ibiza
        Shopware()->Container()->get('dbal_connection')->exec(file_get_contents(__DIR__ . '/_fixtures/pseudo_price.sql'));

        Shopware()->Modules()->Basket()->sDeleteBasket();
        Shopware()->Modules()->Basket()->sAddArticle($product1, 1);
        Shopware()->Modules()->Basket()->sAddArticle($product2, 1);

        $result = $productDataProvider->get([$product1 => 1, $product2 => 1]);

        static::assertSame($result[$product1]['price::pseudoprice'], '0');
    }

    private function getProductDataProvider(): ProductDataProvider
    {
        return new ProductDataProvider(
            Shopware()->Db(),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );
    }
}
