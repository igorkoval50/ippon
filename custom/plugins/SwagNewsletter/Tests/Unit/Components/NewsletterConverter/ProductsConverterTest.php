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

namespace SwagNewsletter\Tests\Unit\Component\NewsletterConverter\Converter;

use PHPUnit\Framework\TestCase;
use SwagNewsletter\Components\ContainerConverter\ContainerConverterException;
use SwagNewsletter\Components\ContainerConverter\Converter\ProductConverter;

class ProductsConverterTest extends TestCase
{
    /**
     * @var ProductConverter
     */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();

        $this->SUT = new ProductConverter();
    }

    public function testConvert()
    {
        $demoData = $this->getDemoData();

        $result = $this->SUT->convert($demoData);

        $this->assertEquals($this->getExpectedData(), $result);
    }

    public function testConvertThrowsExceptionOnInvalidArray()
    {
        $this->expectException(ContainerConverterException::class);

        $demoData = $this->getDemoData();
        unset($demoData['container']);

        $this->SUT->convert($demoData);
    }

    /**
     * @return array
     */
    private function getDemoData()
    {
        return [
            'container' => [
                'id' => 4,
                'newsletterId' => 2,
                'value' => 'TestValue',
                'type' => 'ctArticles',
                'description' => 'Lorem ipsum donor...',
                'position' => 1,
                'text' => 'This is my demo text to test something.',
                'articles' => [
                    [
                        'id' => 6,
                        'containerId' => 4,
                        'number' => 'SW10002.3',
                        'name' => 'Münsterländer Lagerkorn 32%',
                        'type' => 'fix',
                        'position' => 0,
                    ],
                ],
                'links' => [],
                'banner' => null,
            ],
            'data' => [],
        ];
    }

    /**
     * @return array
     */
    private function getExpectedData()
    {
        return [
            [
                'key' => 'headline',
                'value' => 'Lorem ipsum donor...',
            ],
            [
                'key' => 'article_data',
                'type' => 'json',
                'value' => [
                    [
                        'name' => 'Münsterländer Lagerkorn 32%',
                        'ordernumber' => 'SW10002.3',
                        'position' => 0,
                        'type' => 'fix',
                    ],
                ],
            ],
        ];
    }
}
