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

use SwagCustomProducts\Components\Services\PriceFactory;
use SwagCustomProducts\Models\Price;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class PriceFactoryTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_createDefaultPrice_should_create_default_price()
    {
        $service = $this->createPriceFactory();
        /** @var Price[] $defaultPrice */
        $defaultPrice = $service->createDefaultPrice(1, 1);

        static::assertEquals(0, $defaultPrice[0]->getSurcharge());
        static::assertEquals(1, $defaultPrice[0]->getTaxId());
        static::assertEquals('Shopkunden', $defaultPrice[0]->getCustomerGroupName());
        static::assertEquals(1, $defaultPrice[0]->getCustomerGroupId());
    }

    /**
     * @return PriceFactory
     */
    private function createPriceFactory()
    {
        return new PriceFactory(Shopware()->Container());
    }
}
