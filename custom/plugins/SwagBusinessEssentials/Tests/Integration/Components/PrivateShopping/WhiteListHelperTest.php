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

namespace SwagBusinessEssentials\Tests\Integration\Components\PrivateShopping;

use PHPUnit\Framework\TestCase;
use SwagBusinessEssentials\Components\PrivateShopping\WhiteListHelper;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class WhiteListHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var WhiteListHelper
     */
    private $service;

    public function test_getControllers()
    {
        $controllers = $this->service->getControllers();

        static::assertArraySubset([
            ['key' => 'account', 'name' => 'Account'],
        ], $controllers);
    }

    public function test_isControllerWhitelisted_with_non_existent_controller()
    {
        $result = $this->service->isControllerWhiteListed('EK', 'something', 'index');
        static::assertFalse($result);
    }

    public function test_isControllerWhitelisted_should_validate_BusinessEssentails_controllers()
    {
        $result = $this->service->isControllerWhiteListed('EK', 'PrivateLogin', 'index');
        static::assertTrue($result);
    }

    public function test_isControllerWhitelisted_should_return_false_account_invalid_action()
    {
        $result = $this->service->isControllerWhiteListed('EK', 'account', 'index');
        static::assertFalse($result);
    }

    public function test_isControllerWhitelisted_should_return_true_valid_account_action()
    {
        $result = $this->service->isControllerWhiteListed('EK', 'account', 'login');
        static::assertTrue($result);
    }

    public function test_isControllerWhitelisted_should_return_true_valid_register_action()
    {
        $result = $this->service->isControllerWhiteListed('EK', 'register', 'saveRegister');
        static::assertTrue($result);
    }

    public function test_isControllerWhitelisted_should_return_false_invalid_register_action()
    {
        $result = $this->service->isControllerWhiteListed('EK', 'register', 'index');
        static::assertFalse($result);
    }

    public function test_isControllerWhitelisted_should_return_true_manually_whitelisted()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/PrivateShoppingActive.sql'));

        $result = $this->service->isControllerWhiteListed('EK', 'foo', 'index');
        static::assertTrue($result);
    }

    /**
     * @before
     */
    protected function createServiceBefore()
    {
        $this->service = new WhiteListHelper(
            self::getKernel()->getContainer()->get('snippets'),
            self::getKernel()->getContainer()->get('events'),
            self::getKernel()->getContainer()->get('swag_business_essentials.config_helper')
        );
    }
}
