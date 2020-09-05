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

namespace SwagCustomProducts\Tests\Unit\Components\Inquiry;

use SwagCustomProducts\Components\Inquiry\InquiryService;
use SwagCustomProducts\Components\Inquiry\InquiryServiceInterface;
use SwagCustomProducts\Components\Inquiry\Strategy\StrategyInterface;

class InquiryServiceTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_can_be_created()
    {
        $service = $this->createService();

        static::assertInstanceOf(InquiryService::class, $service);
        static::assertInstanceOf(InquiryServiceInterface::class, $service);
    }

    public function test_generateMessage_should_return_values_message()
    {
        $service = new InquiryService(new InquiryStrategyDummy(), new ValuesStrategyMockReturnsMessage());

        $result = $service->getMessage([], true);

        static::assertEquals('Values strategy', $result);
    }

    public function test_generateMessage_should_return_selected_values_message()
    {
        $service = new InquiryService(new SelectedValuesStrategyMockReturnsMessage(), new InquiryStrategyDummy());

        $result = $service->getMessage([]);

        static::assertEquals('Selected values strategy', $result);
    }

    private function createService()
    {
        return new InquiryService(new InquiryStrategyDummy(), new InquiryStrategyDummy());
    }
}

class InquiryStrategyDummy implements StrategyInterface
{
    public function generateMessage(array $data)
    {
    }
}

class ValuesStrategyMockReturnsMessage implements StrategyInterface
{
    public function generateMessage(array $data)
    {
        return 'Values strategy';
    }
}

class SelectedValuesStrategyMockReturnsMessage implements StrategyInterface
{
    public function generateMessage(array $data)
    {
        return 'Selected values strategy';
    }
}
