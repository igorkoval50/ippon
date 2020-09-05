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
use SwagBusinessEssentials\Components\PrivateShopping\LoginHelper;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class LoginHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var LoginHelper
     */
    private $service;

    public function test_getViewVariables_private_shopping_inactive()
    {
        $result = $this->service->getViewVariables();

        static::assertArrayHasKey('showRegister', $result);
        static::assertArrayNotHasKey('loginUrl', $result);
    }

    public function test_getViewVariables_private_shopping_active()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/PrivateShoppingActive.sql'));
        $result = $this->service->getViewVariables();

        static::assertEquals(1, $result['showRegister']);
        static::assertStringEndsWith('/sTarget/PrivateLogin/sTargetAction/redirectLogin', $result['loginUrl']);
    }

    /**
     * @before
     */
    protected function createServiceBefore()
    {
        $this->service = new LoginHelper(
            self::getContainer()->get('swag_business_essentials.config_helper'),
            self::getContainer()->get('shopware_storefront.context_service'),
            self::getContainer()->get('router'),
            self::getContainer()->get('template'),
            self::getContainer()->get('dbal_connection'),
            self::getContainer()->get('swag_business_essentials.redirect_param_helper')
        );
    }
}
