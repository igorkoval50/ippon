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

use SwagCustomProducts\Components\Services\CustomProductsService;
use SwagCustomProducts\Components\Services\TemplateService;
use SwagCustomProducts\Structs\OptionStruct;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ReflectionHelper;
use SwagCustomProducts\tests\ServicesHelper;
use SwagCustomProducts\tests\TestDataProvider;

class CustomProductsServiceTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var CustomProductsService
     */
    private $customProductsService;

    /**
     * @var TestDataProvider
     */
    private $testDataProvider;

    /**
     * @dataProvider isValidBasketPosition_dataProvider
     */
    public function test_isValidBasketPosition(array $basketPosition, $expectedResult)
    {
        $method = ReflectionHelper::getMethod(CustomProductsService::class, 'isValidBasketPosition');
        $result = $method->invoke($this->getCustomProductService(), $basketPosition);

        static::assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function isValidBasketPosition_dataProvider()
    {
        return [
            [[], false],
            [['quantity' => 1, 'price' => 1.0], false],
            [['quantity' => 1, 'netprice' => 0.8], false],
            [['price' => 1.0, 'netprice' => 0.8], false],
            [['quantity' => 1], false],
            [['netprice' => 1], false],
            [['price' => 1], false],
            [['quantity', 'price', 'netprice'], false],
            [['quantity' => 1, 'price' => 1.0, 'netprice' => 0.8], true],
            [['quantity' => 0, 'price' => 0.0, 'netprice' => 0.0], true],
        ];
    }

    /**
     * @dataProvider recalculatePercentageOptionPriceForBlockPrices_dataProvider
     */
    public function test_recalculatePercentageOptionPriceForBlockPrices(array $option, array $basketPosition, array $expectedResult)
    {
        $method = ReflectionHelper::getMethod(CustomProductsService::class, 'recalculatePercentageOptionPriceForBlockPrices');
        $result = $method->invokeArgs($this->getCustomProductService(), [$option, $basketPosition]);

        static::assertArraySubset($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function recalculatePercentageOptionPriceForBlockPrices_dataProvider()
    {
        // option, basketPosition, result
        return [
            [
                [],
                ['quantity' => 1],
                [],
            ],
            [
                ['prices' => []],
                ['quantity' => 2],
                [],
            ],
            [
                ['could_contain_values' => 0, 'prices' => [['percentage' => 0]]],
                ['quantity' => 2],
                [],
            ],
            [
                ['could_contain_values' => 0, 'prices' => [['percentage' => 10, 'customer_group_id' => 1]]],
                ['quantity' => 2, 'price' => 10.0, 'netprice' => 1.0],
                ['surcharge' => 1.0, 'netPrice' => 0.10000000000000001],
            ],
            [
                ['could_contain_values' => 0, 'prices' => [['percentage' => 10, 'customer_group_id' => 1]]],
                ['quantity' => 2, 'price' => 20.0, 'netprice' => 2.0],
                ['surcharge' => 2.0, 'netPrice' => 0.20000000000000001],
            ],
            [
                ['could_contain_values' => 0, 'prices' => [['percentage' => 10, 'customer_group_id' => 1]]],
                ['quantity' => 2, 'price' => 39.95, 'netprice' => 33.571428571429],
                ['surcharge' => 3.9950000000000001, 'netPrice' => 3.3571428571429003],
            ],
        ];
    }

    public function test_get_option_by_id()
    {
        $this->beforeTest();

        $expectedValue = 'fancy_mountain.jpg';
        $expectedOptionName = 'Choose your favourite motives!';

        $grossPrices = false;
        $customerConfiguration = $this->testDataProvider->getCustomerConfiguration();

        $fetchedOption = $this->customProductsService->getOptionById(TestDataProvider::FAVOURITE_MOTIVES_OPTION_ID, $customerConfiguration, $grossPrices);

        static::assertEquals($expectedOptionName, $fetchedOption['name'], "Couldn't get the correct option by id, expected {$expectedOptionName}, got {$fetchedOption['name']}");

        $resultValue = $fetchedOption['values'][0]['value'];
        static::assertEquals($expectedValue, $fetchedOption['values'][0]['value'], "Couldn't assert the correct customized value, expected {$expectedValue}, got {$resultValue}");
    }

    public function test_get_options_by_configuration()
    {
        $this->beforeTest();

        $expectedAmountOfCustomizedOptions = 2;

        $customerConfiguration = $this->testDataProvider->getCustomerConfiguration();
        $options = $this->customProductsService->getOptionsByConfiguration($customerConfiguration);

        static::assertCount($expectedAmountOfCustomizedOptions, $options, "Couldn't assert the amount of customized options.");
        static::assertContainsOnlyInstancesOf(OptionStruct::class, $options, 'Options are not an instance of ' . OptionStruct::class);
    }

    public function test_check_for_required_options()
    {
        $this->beforeTest();

        $hasRequiredOptions = $this->customProductsService->checkForRequiredOptions(TestDataProvider::CUSTOMIZABLE_PRODUCT_ID);

        static::assertTrue($hasRequiredOptions, 'Failed checking for requierd products, expected required options but found nothing.');
    }

    public function test_check_for_required_options_with_non_customizable_product()
    {
        $this->beforeTest();

        $notCustomizableProductId = 3;
        $hasRequiredOptions = $this->customProductsService->checkForRequiredOptions($notCustomizableProductId);

        static::assertFalse($hasRequiredOptions, "Expected that products without a configured custom products template don't have required options.");
    }

    public function test_check_for_required_options_without_required_options()
    {
        $this->beforeTest();

        $templateService = $this->getMockBuilder(TemplateService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $templateService->method('getTemplateByProductId')
            ->willReturn(['options' => [['required' => false], ['required' => false]]]);

        $customProductsService = new CustomProductsService(
            Shopware()->Container(),
            $templateService,
            Shopware()->Container()->get('dbal_connection'),
            Shopware()->Container()->get('custom_products.custom_products_option_repository'),
            Shopware()->Container()->get('custom_products.product_price_getter'),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        $hasRequiredOptions = $customProductsService->checkForRequiredOptions(TestDataProvider::CUSTOMIZABLE_PRODUCT_ID);

        static::assertFalse($hasRequiredOptions, 'Expected 0 required options but found at least one.');
    }

    private function beforeTest()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();

        /* @var TestDataProvider $testDataProvider */
        $this->testDataProvider = Shopware()->Container()->get('swag_custom_products.test_data_provider');
        $this->testDataProvider->setUp();

        $this->customProductsService = $this->getCustomProductService();
    }

    private function getCustomProductService()
    {
        return new CustomProductsService(
            Shopware()->Container()->get('service_container'),
            Shopware()->Container()->get('custom_products.template_service'),
            Shopware()->Container()->get('dbal_connection'),
            Shopware()->Container()->get('custom_products.custom_products_option_repository'),
            Shopware()->Container()->get('custom_products.product_price_getter'),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );
    }
}
