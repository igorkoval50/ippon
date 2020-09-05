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
use SwagNewsletter\Components\ContainerConverter\Converter\SuggestConverter;

class SuggestConverterTest extends TestCase
{
    /**
     * @var SuggestConverter
     */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();

        $this->SUT = new SuggestConverter();
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
        unset($demoData['container']['description']);
        unset($demoData['container']['value']);

        $this->SUT->convert($demoData);
    }

    /**
     * @return array
     */
    private function getDemoData()
    {
        return [
            'container' => [
                'id' => 21,
                'newsletterId' => 4,
                'value' => 4,
                'type' => 'ctSuggest',
                'description' => 'TEST',
                'position' => 2,
                'text' => '',
                'articles' => [],
                'links' => [],
                'banner' => '',
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
                'key' => 'headline',
                'value' => 'TEST',
            ],
            [
                'key' => 'number',
                'value' => 4,
            ],
        ];
    }
}
