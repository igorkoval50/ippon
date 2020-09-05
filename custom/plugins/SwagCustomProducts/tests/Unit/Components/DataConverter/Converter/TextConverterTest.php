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

namespace SwagCustomProducts\Tests\Unit\Components\DataConverter\Converter;

use SwagCustomProducts\Components\DataConverter\Converter\TextConverter;
use SwagCustomProducts\Components\DataConverter\ConverterInterface;

class TextConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TextConverter
     */
    private $service;

    /**
     * @before
     */
    public function createServiceBefore()
    {
        $this->service = new TextConverter();
    }

    public function test_it_can_be_created()
    {
        static::assertInstanceOf(TextConverter::class, $this->service);
        static::assertInstanceOf(ConverterInterface::class, $this->service);
    }

    public function test_convertRequestData()
    {
        $data = 'test';

        $result = $this->service->convertRequestData($data);

        static::assertEquals([
            'test',
        ], $result);
    }

    public function test_convertBasketData()
    {
        $optionData = [];
        $data = [
            'test',
        ];

        $result = $this->service->convertBasketData($optionData, $data);

        static::assertArrayHasKey('selectedValue', $result);
        static::assertEquals([
            'selectedValue' => ['test'],
        ], $result);
    }
}
