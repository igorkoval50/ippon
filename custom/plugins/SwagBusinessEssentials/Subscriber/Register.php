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

namespace SwagBusinessEssentials\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action as BaseController;
use Enlight_Controller_ActionEventArgs as ActionEventArgs;
use Enlight_Controller_Request_Request as RequestInterface;
use Enlight_Event_EventArgs as EventArgs;
use RuntimeException;
use SwagBusinessEssentials\Components\DependencyProvider;
use SwagBusinessEssentials\Components\PrivateRegister\RegistrationHelperInterface;
use SwagBusinessEssentials\Components\PrivateShopping\LoginHelperInterface;
use SwagBusinessEssentials\Components\PrivateShopping\ShopAccessHelperInterface;

class Register implements SubscriberInterface
{
    /**
     * @var RegistrationHelperInterface
     */
    protected $registrationHelper;

    /**
     * @var DependencyProvider
     */
    private $dependencyProvider;

    /**
     * @var LoginHelperInterface
     */
    private $loginHelper;

    public function __construct(
        RegistrationHelperInterface $registrationHelper,
        DependencyProvider $dependencyProvider,
        LoginHelperInterface $loginHelper
    ) {
        $this->registrationHelper = $registrationHelper;
        $this->dependencyProvider = $dependencyProvider;
        $this->loginHelper = $loginHelper;
    }

    /**
     * Returns the necessary events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'registerTemplate',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Register' => [
                ['onPostDispatchRegister'],
                ['onLoginPanel', 1],
                ['afterSaveRegister', 2],
            ],
            'Shopware_Modules_Admin_SaveRegister_Successful' => 'onSuccessfulRegister',
        ];
    }

    /**
     * Used to inject the custom template per customer group
     */
    public function registerTemplate()
    {
        if (!$this->dependencyProvider->hasShop()) {
            return;
        }

        /** @var \sAdmin $adminModule */
        $adminModule = $this->dependencyProvider->getModule('admin');
        if (!$adminModule->sCheckUser()) {
            return;
        }

        $shop = $this->dependencyProvider->getShop();
        if ($shop === null) {
            return;
        }
        $userData = $adminModule->sGetUserData();
        $this->registrationHelper->registerTheme($userData['additional']['user']['customergroup'], $shop);
    }

    /**
     * Throws an exception if an invalid register page was opened.
     * If a custom template was configured, it will be loaded.
     *
     * @throws \Exception
     */
    public function onPostDispatchRegister(ActionEventArgs $args)
    {
        $subject = $args->getSubject();
        $request = $subject->Request();
        $view = $subject->View();

        if ($request->getActionName() !== 'index' || $this->isOnConfirmPage($request)) {
            return;
        }

        $view->addTemplateDir(dirname(__DIR__) . '/Resources/views');

        $shop = $this->dependencyProvider->getShop();
        if ($shop === null) {
            return;
        }

        $customerGroup = $shop->getCustomerGroup()->getKey();
        if ($request->has('sValidation')) {
            $customerGroup = $request->getParam('sValidation');
        } else {
            // After an unsuccessful form validation, the parameter will be removed from the URL,
            // but the sValidation key is still available in the view assigns.
            $registration = $view->getAssign('register');

            if (isset($registration['personal']['sValidation'])) {
                $customerGroup = $registration['personal']['sValidation'];
            }
        }

        if (!$this->registrationHelper->isRegistrationAllowed($customerGroup, $subject->get('shop')->getId())) {
            throw new RuntimeException("Registration for the customer group {$customerGroup} is not allowed.");
        }

        $targetUrl = $this->registrationHelper->getTargetUrl($customerGroup, $subject->get('shop'));

        if ($targetUrl) {
            $view->assign('registerRedirectUrl', $targetUrl);
        }

        $templatePath = $this->registrationHelper->getTemplate($customerGroup);

        if ($templatePath === false) {
            return;
        }

        if ($templatePath === null) {
            throw new RuntimeException('The configured template file could not be found.');
        }

        $view->loadTemplate($templatePath);
    }

    /**
     * Necessary to immediately log out the user again if the registration wasn't supposed to log the user into his account.
     */
    public function onSuccessfulRegister(EventArgs $eventArgs)
    {
        $customerGroup = $this->registrationHelper->getValidationCustomerGroup($eventArgs->get('id'));

        if (!$customerGroup) {
            return;
        }

        $shop = $this->dependencyProvider->getShop();
        if ($shop === null) {
            return;
        }

        if (!$this->registrationHelper->isConfirmationNeeded($customerGroup, $shop)) {
            return;
        }

        /** @var \sAdmin $adminModule */
        $adminModule = $this->dependencyProvider->getModule('admin');
        $adminModule->logout();
    }

    public function onLoginPanel(ActionEventArgs $args)
    {
        $subject = $args->getSubject();
        $request = $subject->Request();
        $view = $subject->View();

        if ($request->getActionName() !== 'index' || $this->isOnConfirmPage($request)) {
            return;
        }

        $view->assign($this->loginHelper->getViewVariables());
    }

    public function afterSaveRegister(ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $errors = $controller->View()->getAssign('errors');

        /** @var \Enlight_Components_Session_Namespace $session */
        $session = $controller->get('session');

        if (!$errors && $session->offsetExists('PrivateShoppingRegisterAssign')) {
            $session->offsetUnset('PrivateShoppingRegisterAssign');
        }

        if ($this->isNoErrorOnPrivateShoppingPage($controller)) {
            return;
        }

        $viewAssign = $controller->View()->getAssign();

        unset(
            $viewAssign['register']['personal']['password'],
            $viewAssign['register']['personal']['passwordConfirmation'],
            $viewAssign['register']['personal']['emailConfirmation']
        );

        $controller->get('session')->offsetSet('PrivateShoppingRegisterAssign', $controller->View()->getAssign());

        $controller->redirect([
            'controller' => 'PrivateLogin',
            'action' => 'index',
        ]);
    }

    /**
     * Checks if an error occurred on the private shopping register page.
     *
     * @return bool
     */
    private function isNoErrorOnPrivateShoppingPage(BaseController $controller)
    {
        $errors = $controller->View()->getAssign('errors');
        /** @var ShopAccessHelperInterface $shopAccessHelper */
        $shopAccessHelper = $controller->get('swag_business_essentials.shop_access_helper');

        return $controller->Request()->getActionName() !== 'index'
            || !$errors
            || !$shopAccessHelper->isPrivateShoppingActive($this->dependencyProvider->getShop()->getCustomerGroup()->getKey());
    }

    /**
     * Checks if the user was on the checkout confirm page before getting redirected.
     *
     * @return bool
     */
    private function isOnConfirmPage(RequestInterface $request)
    {
        return $request->getParam('controller') === 'checkout'
            && in_array($request->getParam('action'), ['confirm', 'shippingPayment'], true);
    }
}
