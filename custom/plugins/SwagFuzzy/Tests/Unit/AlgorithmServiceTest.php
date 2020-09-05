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

namespace SwagFuzzy\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SwagFuzzy\Components\AlgorithmService;
use SwagFuzzy\Components\SettingsService;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class AlgorithmServiceTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var AlgorithmService
     */
    private $SUT;

    public function setUp(): void
    {
        parent::setUp();

        $settingsServiceMock = $this->getMockBuilder(SettingsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $settingsServiceMock->method('getSettings')
            ->willReturn(['searchDistance' => 20]);

        $this->SUT = new AlgorithmService($settingsServiceMock);
    }

    public function testDoLevenshtein()
    {
        $expectedSearchDistance = 25;

        $result = $this->SUT->doLevenshtein('bad', 'backform', 'backform');

        $this->assertEquals($expectedSearchDistance, $result);
    }

    public function testDoSimilarText()
    {
        $expectedSearchDistance = 25;

        $result = $this->SUT->doSimilarText('bad ', 'backform', 'backform');

        $this->assertEquals($expectedSearchDistance, $result);
    }
}
