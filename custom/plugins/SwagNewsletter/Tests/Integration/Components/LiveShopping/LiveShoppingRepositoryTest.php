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

namespace SwagNewsletter\Tests\Integration\Components\LiveShopping;

use PHPUnit\Framework\TestCase;
use Shopware\Models\Plugin\Plugin;
use SwagNewsletter\Tests\LiveShoppingCompatiblityTestCaseTrait;

class LiveShoppingRepositoryTest extends TestCase
{
    use LiveShoppingCompatiblityTestCaseTrait;

    public function test_getLiveShopping_should_return_LiveShopping()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/demo.sql'));

        $liveShoppingRepository = self::getContainer()->get('swag_newsletter.components.live_shopping_repository');

        $plugin = $liveShoppingRepository->getLiveShoppingPlugin();

        $this->assertEquals('SwagLiveShopping', $plugin->getName());
        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
