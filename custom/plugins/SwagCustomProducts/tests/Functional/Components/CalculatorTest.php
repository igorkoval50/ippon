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

namespace SwagCustomProducts\Tests\Functional\Components;

use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\Customer\Group;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;
use Shopware\Models\Customer\Address;
use SwagCustomProducts\Components\Calculator;
use SwagCustomProducts\Components\Services\LiveShoppingHelper;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class CalculatorTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_getPrice_should_throw_exception_no_tax_id()
    {
        /** @var ContextServiceInterface $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');

        $this->expectExceptionMessage('Cannot proceed without a tax ID');

        $calculator = $this->getCalculator();

        $calculator->getPrice(
            ['surcharge' => 15],
            $contextService->getShopContext(),
            0.00
        );
    }

    public function test_getPrice_should_grant_percentage_discount()
    {
        /** @var ContextServiceInterface $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $shopContext = $contextService->getShopContext();
        $customerGroup = $shopContext->getCurrentCustomerGroup();

        $customerGroup->setUseDiscount(true);
        $customerGroup->setPercentageDiscount(50);

        $calculator = $this->getCalculator();

        $price = $calculator->getPrice(
            [
                'surcharge' => 15,
                'tax_id' => 1,
                'is_percentage_surcharge' => false,
            ],
            $shopContext,
            0.00
        );

        static::assertEquals(7.5, $price['netPrice']);
    }

    public function test_getPrice_should_return_taxfree_price()
    {
        /** @var ContextServiceInterface $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $shopContext = $contextService->getShopContext();
        $customerGroup = $shopContext->getCurrentCustomerGroup();

        $customerGroup->setUseDiscount(true);
        $customerGroup->setPercentageDiscount(50);

        $calculator = $this->getCalculator();
        $addressId = 3;

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', $addressId);

        /** @var Address $address */
        $address = Shopware()->Container()->get('models')->find(Address::class, $addressId);
        $address->getCountry()->setTaxFree(1);

        $price = $calculator->getPrice(
            [
                'surcharge' => 15,
                'tax_id' => 1,
                'is_percentage_surcharge' => false,
            ],
            $shopContext,
            0.00,
            true
        );

        static::assertEquals(7.5, $price['surcharge']);
    }

    public function test_calculate()
    {
        $calculator = $this->getCalculator();
        $options = require __DIR__ . '/_fixtures/options.php';
        $configuration = require __DIR__ . '/_fixtures/configuration.php';

        $result = $calculator->calculate($options, $configuration, 'SW10178');

        $expectedSubset = require __DIR__ . '/_fixtures/expectedPriceResult.php';

        foreach ($result['surcharges'] as $index => $surcharge) {
            static::assertArraySubset($expectedSubset['surcharges'][$index], $surcharge);
        }

        static::assertSame(10.969999999999999, $result['totalPriceSurcharges']);
        static::assertSame(0.0, $result['totalPriceOnce']);
        static::assertSame(19.95, $result['basePrice']);
        static::assertSame(30.919999999999998, $result['totalUnitPrice']);
        static::assertSame(30.919999999999998, $result['total']);
    }

    public function test_getPrice_throws_exception()
    {
        $calculator = $this->getCalculator();
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $surcharge = [
            'tax_id' => 0,
            'surcharge' => 10.00,
            'is_percentage_surcharge' => false,
        ];

        $this->expectException(\Exception::class);

        $calculator->getPrice($surcharge, $context, 0.00);
    }

    public function test_getPrice_use_discount()
    {
        $calculator = $this->getCalculator();

        /** @var ShopContext */
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1, 1, 'H');

        $customerGroup = new Group();
        $customerGroup->setId(1);
        $customerGroup->setKey('EK');
        $customerGroup->setName('CustomerGroup');
        $customerGroup->setDisplayGrossPrices(true);
        $customerGroup->setInsertedGrossPrices(false);
        $customerGroup->setUseDiscount(true);
        $customerGroup->setMinimumOrderValue(1);
        $customerGroup->setSurcharge(10.00);
        $customerGroup->setPercentageDiscount(10.00);

        $reflectionClass = new \ReflectionClass(ShopContext::class);
        $property = $reflectionClass->getProperty('currentCustomerGroup');
        $property->setAccessible(true);

        $property->setValue($context, $customerGroup);

        $surcharge = [
            'tax_id' => 1,
            'surcharge' => 10.00,
            'is_percentage_surcharge' => false,
        ];

        $expectedResult = [
            'netPrice' => 9.0,
            'surcharge' => 10.71,
            'tax_id' => 1,
            'tax' => 1.71,
            'isTaxFreeDelivery' => false,
        ];

        $result = $calculator->getPrice($surcharge, $context, 10.00);

        static::assertArraySubset($expectedResult, $result);
    }

    public function test_getPrice_has_free_delivery()
    {
        $calculator = $this->getCalculator();

        /** @var ShopContext */
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1, 1, 'H');

        $customerGroup = new Group();
        $customerGroup->setId(1);
        $customerGroup->setKey('EK');
        $customerGroup->setName('CustomerGroup');
        $customerGroup->setDisplayGrossPrices(false);
        $customerGroup->setInsertedGrossPrices(false);
        $customerGroup->setUseDiscount(true);
        $customerGroup->setMinimumOrderValue(1);
        $customerGroup->setSurcharge(10.00);
        $customerGroup->setPercentageDiscount(10.00);

        $reflectionClass = new \ReflectionClass(ShopContext::class);
        $property = $reflectionClass->getProperty('currentCustomerGroup');
        $property->setAccessible(true);

        $property->setValue($context, $customerGroup);

        $surcharge = [
            'tax_id' => 1,
            'surcharge' => 10.00,
            'is_percentage_surcharge' => false,
        ];

        $expectedResult = [
            'netPrice' => 9.0,
            'surcharge' => 9.0,
            'tax_id' => 1,
            'tax' => 1.71,
            'isTaxFreeDelivery' => false,
        ];

        $result = $calculator->getPrice($surcharge, $context, 10.00, true);

        static::assertArraySubset($expectedResult, $result);
    }

    public function test_add()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('add');
        $method->setAccessible(true);

        $method->invoke($calculator, true, 'test', 10.00, 8.00);

        $property = $reflectionClass->getProperty('onceSurchargesArray');
        $property->setAccessible(true);
        $result = $property->getValue($calculator);

        $expectedResult = [
            'name' => 'test',
            'price' => 10.0,
            'netPrice' => 8.0,
            'tax' => 2.0,
            'isParent' => false,
            'hasParent' => false,
            'hasSurcharge' => true,
        ];

        static::assertArraySubset($expectedResult, array_shift($result));
    }

    public function test_iterate_is_once_surcharge()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('iterate');
        $method->setAccessible(true);

        $configuration = [
            'option_1' => ['value_1' => ['id' => 12]],
            'option_2' => ['value_2' => ['id' => 12]],
        ];

        $options = [
            [
                'id' => 'option_1',
                'name' => 'option_1',
                'could_contain_values' => true,
                'is_once_surcharge' => true,
                'surcharge' => 10.00,
                'netPrice' => 8.00,
                'values' => [
                    'value_1' => [
                        'id' => 'value_1',
                        'name' => 'value_1',
                        'surcharge' => false,
                    ],
                ],
            ],  [
                'id' => 'option_2',
                'name' => 'option_2',
                'could_contain_values' => true,
                'is_once_surcharge' => false,
                'surcharge' => 10.00,
                'netPrice' => 8.00,
                'values' => [
                    'value_2' => [
                        'id' => 'value_2',
                        'surcharge' => false,
                    ],
                ],
            ],
        ];

        $method->invoke($calculator, $options, $configuration);

        $property = $reflectionClass->getProperty('onceSurchargesArray');
        $property->setAccessible(true);

        $expectedResult = [
            'name' => 'option_1',
            'price' => 10.0,
            'netPrice' => 8.0,
            'tax' => 2.0,
            'isParent' => false,
            'hasParent' => false,
            'hasSurcharge' => true,
        ];

        $result = $property->getValue($calculator);

        static::assertArraySubset($expectedResult, array_shift($result));
    }

    public function test_handleValues_value_and_option_is_once_surcharge()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('handleValues');
        $method->setAccessible(true);

        $option = [
            'is_once_surcharge' => true,
            'name' => 'test_option',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $value = [
            'is_once_surcharge' => true,
            'name' => 'test_value',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $expectedResult = [
            true,
            false,
        ];

        $result = $method->invoke($calculator, $value, false, $option, false);

        static::assertArraySubset($expectedResult, $result);
    }

    public function test_handleValues_value_is_once_surcharge()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('handleValues');
        $method->setAccessible(true);

        $option = [
            'is_once_surcharge' => false,
            'name' => 'test_option',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $value = [
            'is_once_surcharge' => true,
            'name' => 'test_value',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $expectedResult = [
            true,
            false,
        ];

        $result = $method->invoke($calculator, $value, false, $option, false);

        static::assertArraySubset($expectedResult, $result);
    }

    public function test_handleValues_option_is_once_surcharge()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('handleValues');
        $method->setAccessible(true);

        $option = [
            'is_once_surcharge' => true,
            'name' => 'test_option',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $value = [
            'is_once_surcharge' => false,
            'name' => 'test_value',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $expectedResult = [
            false,
            true,
        ];

        $result = $method->invoke($calculator, $value, false, $option, false);

        static::assertArraySubset($expectedResult, $result);
    }

    public function test_handleValues_has_no_once_surcharge()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('handleValues');
        $method->setAccessible(true);

        $option = [
            'is_once_surcharge' => false,
            'name' => 'test_option',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $value = [
            'is_once_surcharge' => false,
            'name' => 'test_value',
            'surcharge' => 10.00,
            'netPrice' => 8.00,
        ];

        $expectedResult = [
            false,
            true,
        ];

        $result = $method->invoke($calculator, $value, false, $option, false);

        static::assertArraySubset($expectedResult, $result);
    }

    public function test_getTaxId_mode_is_option()
    {
        $calculator = $this->getCalculator();

        $this->installCustomProduct();

        $result = $calculator->getTaxId($calculator::MODE_OPTION, 5);

        static::assertSame('1', $result);
    }

    public function test_getTaxId_mode_is_value()
    {
        $calculator = $this->getCalculator();

        $this->installCustomProduct();

        $result = $calculator->getTaxId($calculator::MODE_VALUE, 5);

        static::assertSame('1', $result);
    }

    public function test_getTaxId_no_mode()
    {
        $calculator = $this->getCalculator();

        $this->installCustomProduct();

        $result = $calculator->getTaxId(111, 5);

        static::assertEmpty($result);
    }

    public function test_getProductPrice()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('getProductPrice');
        $method->setAccessible(true);

        $this->prepareContextService();

        $result = $method->invoke($calculator, 'SW10178', 1);

        static::assertSame(15.09, $result);
    }

    public function test_getPrice()
    {
        $calculator = $this->getCalculator();
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);

        $priceCollection = [];

        foreach ($this->getOptionPrices() as $optionPrice) {
            $priceCollection[] = $calculator->getPrice($optionPrice, $context, 19.95);
        }

        foreach ($this->getValuePrices() as $valuePrice) {
            $priceCollection[] = $calculator->getPrice($valuePrice, $context, 19.95);
        }

        $expectedPrices = require __DIR__ . '/_fixtures/expectedPrices.php';
        foreach ($priceCollection as $index => $priceResult) {
            static::assertArraySubset($expectedPrices[$index], $priceResult);
        }
    }

    public function test_getDeliveryAddress_checkoutShippingAddressId_is_set()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('getDeliveryAddress');
        $method->setAccessible(true);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', 1);

        $result = $method->invoke($calculator);

        static::assertInstanceOf(Address::class, $result);
    }

    public function test_getDeliveryAddress_sUserId_is_set()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('getDeliveryAddress');
        $method->setAccessible(true);

        Shopware()->Container()->get('session')->offsetSet('sUserId', 1);

        $result = $method->invoke($calculator);

        static::assertInstanceOf(Address::class, $result);
    }

    public function test_isTaxFreeDelivery_no_country()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('isTaxFreeDelivery');
        $method->setAccessible(true);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', 1);

        $sql = 'UPDATE s_user_addresses SET country_id = 26 WHERE id = 1;';

        $this->execSql($sql);

        $result = $method->invoke($calculator);

        static::assertTrue($result);
    }

    public function test_isTaxFreeDelivery_should_be_false()
    {
        $calculator = $this->getCalculator();

        $reflectionClass = new \ReflectionClass(Calculator::class);
        $method = $reflectionClass->getMethod('isTaxFreeDelivery');
        $method->setAccessible(true);

        Shopware()->Container()->get('session')->offsetSet('checkoutShippingAddressId', 1);

        $result = $method->invoke($calculator);

        static::assertFalse($result);
    }

    private function prepareContextService()
    {
        $customerGroup = new Group();
        $customerGroup->setId(1);
        $customerGroup->setKey('EK');
        $customerGroup->setName('CustomerGroup');
        $customerGroup->setDisplayGrossPrices(false);
        $customerGroup->setInsertedGrossPrices(false);
        $customerGroup->setUseDiscount(true);
        $customerGroup->setMinimumOrderValue(1);
        $customerGroup->setSurcharge(10.00);
        $customerGroup->setPercentageDiscount(10.00);

        /** @var ContextService $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');

        $reflectionContextServiceClass = new \ReflectionClass(ContextService::class);
        $contextServiceProperty = $reflectionContextServiceClass->getProperty('context');
        $contextServiceProperty->setAccessible(true);

        /** @var ShopContext */
        $context = $contextService->createShopContext(1, 1, 'H');

        $reflectionCustomerGroupClass = new \ReflectionClass(ShopContext::class);
        $customerGroupProperty = $reflectionCustomerGroupClass->getProperty('currentCustomerGroup');
        $customerGroupProperty->setAccessible(true);

        $customerGroupProperty->setValue($context, $customerGroup);
        $contextServiceProperty->setValue($contextService, $context);

        Shopware()->Container()->set('shopware_storefront.context_service', $contextService);
    }

    private function getCalculator()
    {
        Shopware()->Container()->set('custom_products.live_shopping_helper', new LiveShoppingHelper());

        return new Calculator(
            Shopware()->Container()
        );
    }

    private function getOptionPrices()
    {
        return [
            [
                'id' => '7',
                'option_id' => '5',
                'value_id' => null,
                'surcharge' => '0',
                'percentage' => '10',
                'is_percentage_surcharge' => '1',
                'tax_id' => '1',
                'customer_group_name' => 'Shopkunden',
                'customer_group_id' => '1',
            ],
            [
                'id' => '11',
                'option_id' => '6',
                'value_id' => null,
                'surcharge' => '0',
                'percentage' => '0',
                'is_percentage_surcharge' => '0',
                'tax_id' => '1',
                'customer_group_name' => 'Shopkunden',
                'customer_group_id' => '1',
            ],
        ];
    }

    private function getValuePrices()
    {
        return [
            [
                'id' => '8',
                'option_id' => null,
                'value_id' => '3',
                'surcharge' => '0',
                'percentage' => '10',
                'is_percentage_surcharge' => '1',
                'tax_id' => '1',
                'customer_group_name' => 'Shopkunden',
                'customer_group_id' => '1',
            ],
            [
                'id' => '9',
                'option_id' => null,
                'value_id' => '4',
                'surcharge' => '0',
                'percentage' => '10',
                'is_percentage_surcharge' => '1',
                'tax_id' => '1',
                'customer_group_name' => 'Shopkunden',
                'customer_group_id' => '1',
            ],
            [
                'id' => '10',
                'option_id' => null,
                'value_id' => '5',
                'surcharge' => '4.2016806722689',
                'percentage' => '0',
                'is_percentage_surcharge' => '0',
                'tax_id' => '1',
                'customer_group_name' => 'Shopkunden',
                'customer_group_id' => '1',
            ],
        ];
    }

    private function installCustomProduct()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/getPrice-installation.sql');
        $this->execSql($sql);
    }
}
