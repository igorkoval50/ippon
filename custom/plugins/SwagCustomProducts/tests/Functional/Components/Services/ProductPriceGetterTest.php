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

namespace SwagCustomProducts\tests\Functional\Components\Services;

use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Customer\Address;
use SwagCustomProducts\Components\Services\LiveShoppingHelper;
use SwagCustomProducts\Components\Services\ProductPriceGetter;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class ProductPriceGetterTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_getProductPriceByNumber_price_is_zero()
    {
        $service = $this->getService();
        $number = 'SW-not-a-number';

        $result = $service->getProductPriceByNumber($number);

        static::assertSame(0.0, $result);
    }

    public function test_getProductPriceByNumber()
    {
        $service = $this->getService();
        $number = 'SW10178';

        $result = $service->getProductPriceByNumber($number);

        static::assertSame(16.76, $result);
    }

    public function test_getProductPriceByNumber_is_free_delivery()
    {
        $service = $this->getService();
        $number = 'SW10178';

        $sql = "UPDATE s_core_customergroups SET discount = 10.00, mode = 1 WHERE id = 1;
                UPDATE s_core_countries SET taxfree = 1 WHERE countryname = 'Deutschland'";
        $this->execSql($sql);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', 2);

        $result = $service->getProductPriceByNumber($number, 1, true);

        static::assertSame(15.09, $result);
    }

    public function test_getDeliveryAddress_has_shipping_address()
    {
        $service = $this->getService();

        $reflectionClass = new \ReflectionClass(ProductPriceGetter::class);
        $function = $reflectionClass->getMethod('getDeliveryAddress');
        $function->setAccessible(true);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', 2);

        $result = $function->invoke($service);

        static::assertInstanceOf(Address::class, $result);
        static::assertSame(2, $result->getId());
    }

    public function test_getDeliveryAddress_has_user()
    {
        $service = $this->getService();

        $reflectionClass = new \ReflectionClass(ProductPriceGetter::class);
        $function = $reflectionClass->getMethod('getDeliveryAddress');
        $function->setAccessible(true);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', null);
        Shopware()->Container()->get('session')->offsetSet('sUserId', 1);

        $result = $function->invoke($service);

        static::assertInstanceOf(Address::class, $result);
        static::assertSame(3, $result->getId());
    }

    public function test_getDeliveryAddress_has_no_session()
    {
        $service = $this->getService();

        $reflectionClass = new \ReflectionClass(ProductPriceGetter::class);
        $property = $reflectionClass->getProperty('container');
        $property->setAccessible(true);
        $property->setValue($service, new ProductPriceGetterTest_containerMock(Shopware()->Container()));

        $function = $reflectionClass->getMethod('getDeliveryAddress');
        $function->setAccessible(true);

        $result = $function->invoke($service);

        static::assertNull($result);
    }

    public function test_isTaxFreeDelivery()
    {
        $service = $this->getService();

        $reflectionClass = new \ReflectionClass(ProductPriceGetter::class);
        $function = $reflectionClass->getMethod('isTaxFreeDelivery');
        $function->setAccessible(true);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', 1);
        Shopware()->Container()->get('session')->offsetSet('sUserId', 1);

        $result = $function->invoke($service);

        static::assertFalse($result);
    }

    public function test_isTaxFreeDelivery_is_tax_free()
    {
        $service = $this->getService();

        $reflectionClass = new \ReflectionClass(ProductPriceGetter::class);
        $function = $reflectionClass->getMethod('isTaxFreeDelivery');
        $function->setAccessible(true);

        $sql = "UPDATE s_core_countries SET taxfree = 1 WHERE countryname = 'Deutschland'";
        $this->execSql($sql);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', 2);

        $result = $function->invoke($service);

        static::assertTrue($result);
    }

    /**
     * @return ProductPriceGetter
     */
    private function getService()
    {
        $container = Shopware()->Container();
        $container->set('custom_products.live_shopping_helper', new LiveShoppingHelper());

        return new ProductPriceGetter(
            $container->get('shopware_storefront.list_product_service'),
            $container->get('shopware_storefront.context_service'),
            $container->get('models'),
            $container
        );
    }
}

class ProductPriceGetterTest_containerMock
{
    public $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function has()
    {
        return false;
    }
}
