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

namespace SwagCustomProducts\tests\Functional\Components\Cart;

use PHPUnit\Framework\TestCase;
use Shopware\Components\Cart\CartMigrationInterface;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class CartMigrationDecoratorTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_migrate(): void
    {
        $sessionIdAfterLogin = 'newPHPUnitTestSessionId';

        $deleteSql = 'DELETE FROM s_order_basket; DELETE FROM s_order_basket_attributes';
        Shopware()->Container()->get('dbal_connection')->exec($deleteSql);

        $basketSql = file_get_contents(__DIR__ . '/_fixtures/cartPersistServiceDecoratorTest_basket.sql');
        Shopware()->Container()->get('dbal_connection')->exec($basketSql);

        Shopware()->Container()->get('session')->offsetSet('sessionId', $sessionIdAfterLogin);
        Shopware()->Container()->get('session')->offsetSet('sUserId', '1');
        Shopware()->Container()->get('front')->setRequest(new \Enlight_Controller_Request_RequestHttp());

        // Set error reporting to prevent array index exceptions
        $tmp_error_reporting = E_ALL & ~E_NOTICE;
        error_reporting($tmp_error_reporting);

        $this->getMigrationDecorator()->migrate();

        // Reset errorReporting
        error_reporting(E_ALL);

        $resultSql = sprintf(
            'SELECT * FROM s_order_basket WHERE sessionID = "%s" AND modus != 0 AND ordernumber != "SHIPPINGDISCOUNT";',
            $sessionIdAfterLogin
        );
        $result = Shopware()->Container()->get('dbal_connection')->fetchAll($resultSql);

        $expectedProducts = [
            'Bildauswahl',
            'Farbauswahl',
            '#B0AF00',
            'B1',
        ];

        static::assertCount(4, $result);
        foreach ($result as $basketItem) {
            if (!in_array($basketItem['articlename'], $expectedProducts, true)) {
                static::fail('Product not found');
            }
        }
    }

    private function getMigrationDecorator(): CartMigrationInterface
    {
        return Shopware()->Container()->get('shopware.components.cart.cart_migration');
    }
}
