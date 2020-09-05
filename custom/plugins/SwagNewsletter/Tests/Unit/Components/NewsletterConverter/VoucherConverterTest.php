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

namespace SwagNewsletter\Tests\Unit\Component\ContainerConverter;

use PHPUnit\Framework\TestCase;
use SwagNewsletter\Components\ContainerConverter\ContainerConverterException;
use SwagNewsletter\Components\ContainerConverter\Converter\VoucherConverter;

class VoucherConverterTest extends TestCase
{
    /**
     * @var VoucherConverter
     */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();

        $this->SUT = new VoucherConverter();
    }

    public function testConvert()
    {
        $demoData = $this->getDemoData();

        $result = $this->SUT->convert($demoData);

        $this->assertEquals($this->getExpectedResult(), $result);
    }

    public function testConvertThrowsExceptionOnInvalidArray()
    {
        $this->expectException(ContainerConverterException::class);

        $demoData = $this->getDemoData();
        unset($demoData['container']['text']);
        unset($demoData['container']['value']);

        $this->SUT->convert($demoData);
    }

    /**
     * @return array
     */
    public function getDemoData()
    {
        return [
            'container' => [
                'id' => 26,
                'newsletterId' => 26,
                'value' => 4,
                'type' => 'ctVoucher',
                'description' => 'This is my headline',
                'position' => 1,
                'text' => [
                    'id' => 3,
                    'containerId' => 26,
                    'headline' => 'This is my headline.',
                    'content' => 'Vouchercode: {$sVoucher.code}',
                    'image' => '',
                    'link' => 'www.example.org',
                    'alignment' => 'left',
                ],
                'articles' => [],
                'links' => [],
                'banner' => '',
            ],
            'data' => [],
        ];
    }

    /**
     * @return array
     */
    private function getExpectedResult()
    {
        return [
            [
                'key' => 'headline',
                'value' => 'This is my headline',
            ],
            [
                'key' => 'text',
                'value' => 'Vouchercode: {$sVoucher.code}',
            ],
            [
                'key' => 'image',
                'value' => '',
            ],
            [
                'key' => 'url',
                'value' => 'www.example.org',
            ],
            [
                'key' => 'voucher_selection',
                'value' => 4,
            ],
        ];
    }
}
