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

namespace SwagCustomProducts\Tests\Functional\Components\Strategy;

use SwagCustomProducts\Components\Inquiry\Strategy\ValuesStrategy;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;

class ValuesStrategyTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_generateMessage_return_once_price_snippet()
    {
        $valuesStrategy = $this->getValuesStrategy();

        $message = $valuesStrategy->generateMessage([
            'name' => 'foo',
            'surcharge' => 12,
            'is_once_surcharge' => true,
            'values' => [
                [
                    'name' => 'test123',
                    'surcharge' => 12,
                    'is_once_surcharge' => true,
                ],
            ],
        ]);

        static::assertStringContainsString('foo (12,00 &euro; einmalig)', $message);
    }

    public function test_generateMessage_return_name_only_no_surcharge()
    {
        $valuesStrategy = $this->getValuesStrategy();

        $message = $valuesStrategy->generateMessage([
            'name' => 'fooBar',
            'surcharge' => 12,
            'is_once_surcharge' => true,
            'values' => [
            ],
        ]);

        static::assertStringContainsString('fooBar', $message);
    }

    private function getValuesStrategy()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();

        return new ValuesStrategy(
            Shopware()->Container()->get('snippets'),
            Shopware()->Container()->get('shopware_storefront.context_service'),
            Shopware()->Container()->get('custom_products.dependency_provider')
        );
    }
}
