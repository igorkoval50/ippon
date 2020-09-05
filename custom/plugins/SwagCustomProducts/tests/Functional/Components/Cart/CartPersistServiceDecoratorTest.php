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
use Shopware\Components\Cart\CartPersistServiceInterface;
use SwagCustomProducts\Components\Cart\CartPersistServiceDecorator;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ReflectionHelper;

class CartPersistServiceDecoratorTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_prepare(): void
    {
        $this->installBasket();
        Shopware()->Container()->get('session')->offsetSet('sessionId', 'phpUnitFooBarSessionId');

        $cartPersistServiceDecorator = $this->getDecorator();
        $cartPersistServiceDecorator->prepare();

        $basketResult = ReflectionHelper::getProperty(
            CartPersistServiceDecorator::class,
            'basket'
        )->getValue($cartPersistServiceDecorator);

        $basketAttributesResult = ReflectionHelper::getProperty(
            CartPersistServiceDecorator::class,
            'basketAttributes'
        )->getValue($cartPersistServiceDecorator);

        $expectedHash = 'bce4f1887867bd063dca33341683a8f7';
        $expectedProducts = [
            'Bildauswahl',
            'Farbauswahl',
            '#B0AF00',
            'B1',
        ];

        static::assertCount(4, $basketResult);
        static::assertCount(4, $basketAttributesResult);

        foreach ($basketResult as $basketItem) {
            if (!in_array($basketItem['articlename'], $expectedProducts, true)) {
                static::fail('Product not found');
            }
        }

        foreach ($basketAttributesResult as $attributeResult) {
            if ($attributeResult['swag_custom_products_configuration_hash'] !== $expectedHash) {
                static::fail('Unexpected hash');
            }
        }
    }

    public function test_persist(): void
    {
        $this->clearBasket();

        $this->installBasket();
        Shopware()->Container()->get('session')->offsetSet('sessionId', 'phpUnitFooBarSessionId');

        $cartPersistServiceDecorator = $this->getDecorator();
        $cartPersistServiceDecorator->prepare();

        $this->clearBasket();

        $cartPersistServiceDecorator->persist();

        $basketSql = 'SELECT * FROM s_order_basket WHERE modus != 0';
        $basketContent = Shopware()->Container()->get('dbal_connection')->fetchAll($basketSql);

        $basketAttributeSql = 'SELECT * FROM s_order_basket_attributes WHERE swag_custom_products_mode != 1';
        $basketAttributesContent = Shopware()->Container()->get('dbal_connection')->fetchAll($basketAttributeSql);

        $expectedHash = 'bce4f1887867bd063dca33341683a8f7';
        $expectedProducts = [
            'Bildauswahl',
            'Farbauswahl',
            '#B0AF00',
            'B1',
        ];

        static::assertCount(4, $basketContent, 'Basket count fails');
        static::assertCount(4, $basketAttributesContent, 'Attribute count fails');

        foreach ($basketContent as $basketItem) {
            if (!in_array($basketItem['articlename'], $expectedProducts, true)) {
                static::fail('Product not found');
            }
        }

        foreach ($basketAttributesContent as $attributeResult) {
            if ($attributeResult['swag_custom_products_configuration_hash'] !== $expectedHash) {
                static::fail('Unexpected hash');
            }
        }
    }

    private function installBasket(): void
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/cartPersistServiceDecoratorTest_basket.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }

    private function getDecorator(): CartPersistServiceInterface
    {
        return Shopware()->Container()->get('Shopware\Components\Cart\CartPersistServiceInterface');
    }

    /**
     * @return string
     */
    private function clearBasket(): void
    {
        $sql = 'DELETE FROM s_order_basket';
        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }
}
