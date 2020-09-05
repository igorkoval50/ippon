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

namespace SwagBusinessEssentials\Tests\Integration\Subscriber;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Shop\Shop;
use SwagBusinessEssentails\Tests\Integration\SubscriberTestTrait;
use SwagBusinessEssentials\Subscriber\Register;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

require_once __DIR__ . '/../SubscriberTestTrait.php';

class RegisterTest extends TestCase
{
    use KernelTestCaseTrait;
    use SubscriberTestTrait;

    public function test_getSubscribedEvents()
    {
        $events = Register::getSubscribedEvents();

        $subset = [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'registerTemplate',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Register' => [
                ['onPostDispatchRegister'],
                ['onLoginPanel', 1],
                ['afterSaveRegister', 2],
            ],
        ];

        static::assertArraySubset($subset, $events);
    }

    public function test_registerTemplate_should_return_null_no_shop()
    {
        $registerSubscriber = $this->getRegisterSubscriber();
        self::getContainer()->reset('shop');

        self::assertNull($registerSubscriber->registerTemplate(new \Enlight_Controller_ActionEventArgs()));
    }

    public function test_registerTemplate_should_return_null_no_logged_in_user()
    {
        $registerSubscriber = $this->getRegisterSubscriber();

        self::assertNull($registerSubscriber->registerTemplate(new \Enlight_Controller_ActionEventArgs()));
    }

    public function test_registerTemplate_should_register_theme()
    {
        $previousErrorReporting = error_reporting();
        error_reporting($previousErrorReporting & ~E_NOTICE);

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/theme_after_login.sql'));

        $registerSubscriber = $this->getRegisterSubscriber();

        self::getContainer()->get('front')->setRequest(new \Enlight_Controller_Request_RequestTestCase());

        $this->loginCustomer('my_session');

        $args = new \Enlight_Controller_ActionEventArgs();
        $args->set('subject', new ControllerMock(
            Shopware()->Container()->get('template')
        ));

        $registerSubscriber->registerTemplate($args);

        self::assertEquals(22, self::getContainer()->get('shop')->getTemplate()->getId());

        error_reporting($previousErrorReporting);
    }

    public function test_onPostDispatchRegister_should_not_add_template_dir_because_of_wrong_action()
    {
        $previousErrorReporting = error_reporting();
        error_reporting($previousErrorReporting & ~E_NOTICE);

        $registerSubscriber = $this->getRegisterSubscriber();

        $args = $this->getControllerEventArgs();

        $args->getSubject()->Request()->setActionName('wrong_action');
        $args->getSubject()->Request()->setParam('sValidation', 'H');

        $registerSubscriber->onPostDispatchRegister($args);

        $templateDirs = $args->getSubject()->View()->Engine()->getTemplateDir();

        $hasBusinessEssentialsDir = false;

        foreach ($templateDirs as $dir) {
            if (strpos($dir, 'SwagBusinessEssentials/Resources/views') !== false) {
                $hasBusinessEssentialsDir = true;
                break;
            }
        }

        static::assertFalse($hasBusinessEssentialsDir);
    }

    public function test_onPostDispatchRegister_should_add_template_dir_with_customer_group_from_view()
    {
        $previousErrorReporting = error_reporting();
        error_reporting($previousErrorReporting & ~E_NOTICE);

        $registerSubscriber = $this->getRegisterSubscriber();

        $args = $this->getControllerEventArgs();

        $args->getSubject()->Request()->setActionName('index');
        $args->getSubject()->View()->assign('register', [
            'personal' => [
                'sValidation' => 'H',
            ],
        ]);

        $registerSubscriber->onPostDispatchRegister($args);

        $templateDirs = $args->getSubject()->View()->Engine()->getTemplateDir();

        $hasBusinessEssentialsDir = false;

        foreach ($templateDirs as $dir) {
            if (strpos($dir, 'SwagBusinessEssentials/Resources/views') !== false) {
                $hasBusinessEssentialsDir = true;
                break;
            }
        }

        static::assertTrue($hasBusinessEssentialsDir);
    }

    public function test_onPostDispatchRegister_should_throw_exception_because_registration_is_not_allowed()
    {
        $previousErrorReporting = error_reporting();
        error_reporting($previousErrorReporting & ~E_NOTICE);

        $registerSubscriber = $this->getRegisterSubscriber();
        $args = $this->getControllerEventArgs();

        $args->getSubject()->Request()->setActionName('index');
        $args->getSubject()->Request()->setParam('sValidation', 'NOTALLOWED');

        $this->expectExceptionMessage('Registration for the customer group NOTALLOWED is not allowed.');
        $registerSubscriber->onPostDispatchRegister($args);
    }

    public function test_onPostDispatchRegister_should_throw_exception_because_template_does_not_exist()
    {
        $previousErrorReporting = error_reporting();
        error_reporting($previousErrorReporting & ~E_NOTICE);

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/private_register_template.sql'));

        $registerSubscriber = $this->getRegisterSubscriber();
        $args = $this->getControllerEventArgs();

        $args->getSubject()->Request()->setActionName('index');
        $args->getSubject()->Request()->setParam('sValidation', 'H');

        $this->expectExceptionMessage('The configured template file could not be found.');
        $registerSubscriber->onPostDispatchRegister($args);
    }

    public function test_onPostDispatchRegister_should_add_template_dir()
    {
        $previousErrorReporting = error_reporting();
        error_reporting($previousErrorReporting & ~E_NOTICE);

        $registerSubscriber = $this->getRegisterSubscriber();

        $args = $this->getControllerEventArgs();

        $args->getSubject()->Request()->setActionName('index');
        $args->getSubject()->Request()->setParam('sValidation', 'EK');

        $registerSubscriber->onPostDispatchRegister($args);

        $templateDirs = $args->getSubject()->View()->Engine()->getTemplateDir();

        $hasBusinessEssentialsDir = false;

        foreach ($templateDirs as $dir) {
            if (strpos($dir, 'SwagBusinessEssentials/Resources/views') !== false) {
                $hasBusinessEssentialsDir = true;
                break;
            }
        }

        static::assertTrue($hasBusinessEssentialsDir);
    }

    private function loginCustomer($sessionId = 'sessionId')
    {
        /** @var \Shopware\Models\Shop\Repository $shopRepo */
        $shopRepo = self::getContainer()->get('models')->getRepository(Shop::class);
        self::getContainer()->get('shopware.components.shop_registration_service')->registerResources(
            $shopRepo->getActiveDefault()
        );

        /** @var \Enlight_Components_Session_Namespace $session */
        $session = Shopware()->Container()->get('session');
        $session->offsetSet('sUserId', 1);
        $session->offsetSet('sessionId', $sessionId);
        $session->offsetSet('sUserPassword', 'a256a310bc1e5db755fd392c524028a8');
        $session->offsetSet('sUserMail', 'test@example.com');

        /** @var Connection $connection */
        $connection = Shopware()->Container()->get('dbal_connection');
        $connection->executeQuery(
            'UPDATE s_user SET sessionID = :sessionId, lastlogin = now() WHERE id=1',
            [':sessionId' => $sessionId]
        );

        $isCustomerLoggedIn = Shopware()->Modules()->Admin()->sCheckUser();
        static::assertTrue($isCustomerLoggedIn);
    }

    /**
     * @return Register
     */
    private function getRegisterSubscriber()
    {
        return new Register(
            self::getContainer()->get('swag_business_essentials.registration_helper'),
            self::getContainer()->get('swag_business_essentials.dependency_provider'),
            self::getContainer()->get('swag_business_essentials.login_helper')
        );
    }
}

class ControllerMock
{
    /**
     * @var \Enlight_Template_Manager
     */
    private $view;

    public function __construct(\Enlight_Template_Manager $view)
    {
        $this->view = $view;
    }

    public function View()
    {
        return $this->view;
    }
}
