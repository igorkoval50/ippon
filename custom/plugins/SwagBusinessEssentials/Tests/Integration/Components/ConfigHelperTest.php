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

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use SwagBusinessEssentials\Components\ConfigHelper;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class ConfigHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_it_can_be_created()
    {
        $service = new ConfigHelper($this->createMock(Connection::class));
        static::assertInstanceOf(ConfigHelper::class, $service);
    }

    public function test_getConfig_for_private_shopping_table()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));
        $result = $this->createServiceBefore()->getConfig('s_core_plugins_b2b_private', 'activatelogin', 'EK');
        static::assertEquals(1, $result);
    }

    public function test_getConfig_for_cg_settings_table()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));
        $result = $this->createServiceBefore()->getConfig('s_core_plugins_b2b_cgsettings', 'requireunlock', 'H');
        static::assertEquals(1, $result);
    }

    /**
     * @before
     *
     * @return ConfigHelper
     */
    protected function createServiceBefore()
    {
        return new ConfigHelper(self::getContainer()->get('dbal_connection'));
    }
}
