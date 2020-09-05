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

namespace SwagBusinessEssentials\Tests\Integration\Components;

use PHPUnit\Framework\TestCase;
use sBasket;
use Shopware\Models\Shop\Shop;
use SwagBusinessEssentials\Components\DependencyProvider;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class DependencyProviderTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var DependencyProvider
     */
    private $service;

    public function test_getModule()
    {
        $module = $this->service->getModule('Basket');
        static::assertInstanceOf(sBasket::class, $module);
    }

    public function test_hasShop()
    {
        $result = $this->service->hasShop();
        static::assertTrue($result);
    }

    public function test_getShop()
    {
        $result = $this->service->getShop();
        static::assertInstanceOf(Shop::class, $result);
    }

    /**
     * @before
     */
    protected function createServiceBefore()
    {
        $this->service = new DependencyProvider(Shopware()->Container());
    }
}
