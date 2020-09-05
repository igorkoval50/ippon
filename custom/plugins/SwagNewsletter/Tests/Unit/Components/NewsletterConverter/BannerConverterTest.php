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
use SwagNewsletter\Components\ContainerConverter\Converter\BannerConverter;

class BannerConverterTest extends TestCase
{
    /**
     * @var BannerConverter
     */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();

        $this->SUT = new BannerConverter();
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
        unset($demoData['container']['banner']);

        $this->SUT->convert($demoData);
    }

    /**
     * @return array
     */
    private function getDemoData()
    {
        return [
            'container' => [
                'id' => 19,
                'newsletterId' => 3,
                'value' => '',
                'type' => 'ctBanner',
                'description' => 'This is my description.',
                'position' => 3,
                'text' => '',
                'articles' => [],
                'links' => [],
                'banner' => [
                    'id' => 1,
                    'containerId' => 19,
                    'image' => 'media/image/test_image.jpg',
                    'link' => 'www.example.org',
                    'target' => '_blank',
                    'description' => 'This is my banner description.',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    private function getExpectedResult()
    {
        return [
            [
                'key' => 'description',
                'value' => 'This is my description.',
            ],
            [
                'key' => 'file',
                'value' => 'media/image/test_image.jpg',
            ],
            [
                'key' => 'link',
                'value' => 'www.example.org',
            ],
            [
                'key' => 'target_selection',
                'value' => '_blank',
            ],
        ];
    }
}
