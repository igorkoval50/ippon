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

namespace SwagBusinessEssentials\Tests\Integration\Components\PrivateRegister;

use Enlight_Template_Manager as TemplateManager;
use PHPUnit\Framework\TestCase;
use Shopware\Components\Routing\Context;
use Shopware\Components\Routing\Router;
use Shopware\Models\Customer\Group;
use Shopware\Models\Shop\Shop;
use SwagBusinessEssentials\Components\PrivateRegister\RegistrationHelper;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class RegistrationHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var RegistrationHelper
     */
    private $service;

    public function test_isRegistrationAllowed_should_be_allowed_if_customer_group_is_H()
    {
        $result = $this->service->isRegistrationAllowed('H', 1);
        static::assertTrue($result);
    }

    public function test_isRegistrationAllowed_should_be_allowed_if_default_customergroup_is_given()
    {
        $result = $this->service->isRegistrationAllowed('EK', 1);
        static::assertTrue($result);
    }

    public function test_isRegistrationAllowed_should_be_allowed_if_customer_group_is_configured_for_private_shopping()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/IsRegistrationAllowed.sql'));

        $result = $this->service->isRegistrationAllowed('C', 1);
        static::assertTrue($result);
    }

    public function test_isRegistrationAllowed_should_not_allowed_for_invalid_customer_group()
    {
        $result = $this->service->isRegistrationAllowed('X', 1);
        static::assertFalse($result);
    }

    public function test_getTemplate_should_return_false_if_no_template_was_found()
    {
        $result = $this->service->getTemplate('H');
        static::assertFalse($result);
    }

    public function test_getTemplate_should_return_path_if_template_exists()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/IsRegistrationAllowed.sql'));

        $result = $this->service->getTemplate('EK');
        static::assertEquals('', $result);
    }

    public function test_getTargetUrl_private_shopping_inactive()
    {
        $result = $this->service->getTargetUrl('H250', self::getContainer()->get('shop'));

        static::assertEmpty($result);
    }

    public function test_getTargetUrl_private_shopping_active()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/PrivateShoppingActive.sql'));

        $result = $this->service->getTargetUrl('H', self::getContainer()->get('shop'));

        static::assertEquals([
            'controller' => 'register',
            'action' => 'saveRegister',
            'sTarget' => 'test',
            'sTargetAction' => 'index',
        ], $result);
    }

    public function test_getTargetUrl_confirmation_needed()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/PrivateShoppingActiveUnlockRegisterDisabled.sql'));
        $this->execSql(str_replace('H', 'H4', file_get_contents(__DIR__ . '/_fixtures/IsConfirmationNeed.sql')));

        /** @var \Shopware\Models\Shop\Shop $shop */
        $shop = self::getContainer()->get('shop');
        $shop->getCustomerGroup()->setKey('EK22');

        $result = $this->service->getTargetUrl('H4', $shop);

        static::assertEquals([
            'controller' => 'register',
            'action' => 'saveRegister',
            'sTarget' => 'PrivateRegister',
            'sTargetAction' => 'registerConfirm',
        ], $result);
    }

    public function test_getTargetUrl_has_redirect_params()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/PrivateShoppingActive.sql'));
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/RedirectRegistration.sql'));

        $shop = self::getContainer()->get('shop');

        $result = $this->service->getTargetUrl('H5', $shop);

        static::assertEquals([
            'controller' => 'register',
            'action' => 'saveRegister',
            'sTarget' => 'foo',
            'sTargetAction' => 'bar',
        ], $result);
    }

    public function test_isConfirmationNeeded()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/IsConfirmationNeed.sql'));

        $shop = new Shop();
        $shop->setCustomerGroup((new Group())->setKey('H'));

        $result = $this->service->isConfirmationNeeded('H', $shop);

        static::assertTrue($result);
    }

    public function test_getValidationCustomerGroup_if_no_validation_is_configured()
    {
        $result = $this->service->getValidationCustomerGroup(1);

        static::assertEquals('', $result);
    }

    public function test_getValidationCustomerGroup()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/GetValidationCustomerGroup.sql'));

        $result = $this->service->getValidationCustomerGroup(1);

        static::assertEquals('H', $result);
    }

    public function test_getRedirectUlr()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/GetRedirectUrl.sql'));

        $result = $this->service->getRedirectUrl('EK');

        static::assertEquals('http://localhost/account', $result);
    }

    public function test_getRegisterDataFromAssign()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/GetRedirectUrl.sql'));

        $result = $this->service->getRegisterDataFromAssign('', []);

        static::assertEquals([
            'personal' => [
                'sValidation' => false,
            ],
        ], $result);
    }

    public function test_registerTheme()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/RegisterTheme.sql'));

        $shop = new Shop();

        $this->service->registerTheme('EK2', $shop);

        static::assertEquals(22, $shop->getTemplate()->getId());
    }

    public function test_getTemplate_should_return_false_no_template()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/no_register_template.sql'));

        self::assertFalse($this->service->getTemplate('EK55'));
    }

    public function test_getTemplate_should_return_true_available_template_without_tpl()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/custom_register_template_without_tpl.sql'));

        $templateManager = self::getContainer()->get('template');
        $templateManager->addTemplateDir(__DIR__ . '/../../../../Resources/views');

        $registrationHelper = new RegistrationHelper(
            self::getContainer()->get('dbal_connection'),
            self::getContainer()->get('swag_business_essentials.config_helper'),
            $templateManager,
            self::getContainer()->get('models'),
            new RouterMock(),
            self::getContainer()->get('swag_business_essentials.redirect_param_helper')
        );

        $result = $registrationHelper->getTemplate('EK66');

        self::assertEquals('frontend/register/login.tpl', $result);
    }

    public function test_getTemplate_should_return_true_available_template_with_tpl()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/custom_register_template_with_tpl.sql'));

        $templateManager = self::getContainer()->get('template');
        $templateManager->addTemplateDir(__DIR__ . '/../../../../Resources/views');

        $registrationHelper = new RegistrationHelper(
            self::getContainer()->get('dbal_connection'),
            self::getContainer()->get('swag_business_essentials.config_helper'),
            $templateManager,
            self::getContainer()->get('models'),
            new RouterMock(),
            self::getContainer()->get('swag_business_essentials.redirect_param_helper')
        );

        $tpl = $registrationHelper->getTemplate('EK77');

        self::assertEquals('frontend/register/login.tpl', $tpl);
    }

    /**
     * @before
     */
    protected function createServiceBefore()
    {
        $this->service = new RegistrationHelper(
            self::getContainer()->get('dbal_connection'),
            self::getContainer()->get('swag_business_essentials.config_helper'),
            new TemplateManagerMock(),
            self::getContainer()->get('models'),
            new RouterMock(),
            self::getContainer()->get('swag_business_essentials.redirect_param_helper')
        );
    }
}

class TemplateManagerMock extends TemplateManager
{
    public function __construct()
    {
    }
}

class RouterMock extends Router
{
    public function __construct()
    {
    }

    public function assemble($userParams = [], Context $context = null)
    {
        return $userParams;
    }
}
