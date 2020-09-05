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

namespace SwagCustomProducts\Tests\Functional\Components\OrderNumberValidation;

use SwagCustomProducts\Components\OrderNumberValidation\OrderNumberUsedByOptionException;
use SwagCustomProducts\Components\OrderNumberValidation\OrderNumberUsedByProductException;
use SwagCustomProducts\Components\OrderNumberValidation\OrderNumberUsedByValueException;
use SwagCustomProducts\Components\OrderNumberValidation\OrderNumberValidationService;
use SwagCustomProducts\Components\OrderNumberValidation\OrderNumberValidationServiceInterface;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;

class OrderNumberValidationServiceTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;
    const NOT_EXISTING_TEMPLATE_ID = 10;

    public function test_it_can_be_created()
    {
        static::assertInstanceOf(OrderNumberValidationService::class, $this->getService());
        static::assertInstanceOf(OrderNumberValidationServiceInterface::class, $this->getService());
    }

    public function test_validate_should_throw_OrderNumberUsedByProductException()
    {
        $this->expectException(OrderNumberUsedByProductException::class);

        $this->getService()->validate('SW10002.1', 0);
    }

    public function test_validate_should_throw_OrderNumberUsedByOptionException()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));

        $this->expectException(OrderNumberUsedByOptionException::class);

        $this->getService()->validate('custom_option1', self::NOT_EXISTING_TEMPLATE_ID);
    }

    public function test_validate_should_throw_OrderNumberUsedByValueException()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));

        $this->expectException(OrderNumberUsedByValueException::class);

        $this->getService()->validate('custom_value1', self::NOT_EXISTING_TEMPLATE_ID);
    }

    /**
     * @return OrderNumberValidationService
     */
    private function getService()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();

        return Shopware()->Container()->get('custom_products.order_number.validation_service');
    }
}
