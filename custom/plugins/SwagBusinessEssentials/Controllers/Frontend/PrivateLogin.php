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

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\LocationServiceInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Shop\Shop;
use SwagBusinessEssentials\Components\PrivateRegister\RegistrationHelperInterface;
use SwagBusinessEssentials\Components\PrivateShopping\LoginHelperInterface;

class Shopware_Controllers_Frontend_PrivateLogin extends Shopware_Controllers_Frontend_Account
{
    /**
     * Overwritten to prevent unnecessary forwards and unnecessary logic being made in the account-controller.
     */
    public function preDispatch()
    {
    }

    /**
     * Shows the login panel and registers all necessary view variables
     */
    public function indexAction()
    {
        $requireReload = $this->request->getParam('requireReload', false);

        $viewAssign = $this->getPrivateShoppingRegisterAssign();
        if ($viewAssign) {
            $this->get('session')->offsetUnset('PrivateShoppingRegisterAssign');
        }

        $this->logoutAction();

        /** @var LoginHelperInterface $helper */
        $helper = $this->get('swag_business_essentials.login_helper');
        /** @var RegistrationHelperInterface $registrationHelper */
        $registrationHelper = $this->get('swag_business_essentials.registration_helper');
        /** @var Shop $shop */
        $shop = $this->get('shop');

        $errorMessages = $this->Request()->getParam('errorMessages');
        if ($errorMessages) {
            $errorMessages = is_array($errorMessages) ? $errorMessages : [$errorMessages];
        }

        $customerGroupKey = $shop->getCustomerGroup()->getKey();
        $validationCustomerGroup = $registrationHelper->getRegisterGroup($customerGroupKey);
        $targetUrl = $registrationHelper->getTargetUrl($validationCustomerGroup, $shop);

        $view = $this->View();

        $view->loadTemplate($helper->getLoginTpl());
        $view->assign('requireReload', $requireReload);
        $view->assign($helper->getViewVariables());
        $view->assign('errors', $viewAssign['errors']);
        $view->assign('registerRedirectUrl', $targetUrl);
        $view->assign('sErrorMessages', $errorMessages);
        $view->assign('showConfirmation', $this->Request()->getParam('showConfirmation', false));
        $view->assign('showOptinSentMail', $this->Request()->getParam('showOptinSentMail', false));
        $view->assign('countryList', $this->getCountries());
        $view->assign('register', $registrationHelper->getRegisterDataFromAssign($customerGroupKey, $viewAssign['register']));
    }

    /**
     * Provides the login functionality and redirects the user to the proper controller / action afterwards
     */
    public function loginAction()
    {
        parent::loginAction();

        $errorMessages = $this->View()->getAssign('sErrorMessages');

        if (!empty($errorMessages)) {
            $this->forward('index', 'PrivateLogin', 'frontend', ['errorMessages' => $errorMessages]);

            return;
        }

        if (!$this->get('modules')->Admin()->sCheckUser()) {
            return;
        }

        /** @var sAdmin $adminModule */
        $adminModule = $this->get('modules')->Admin();
        $currentCustomerGroup = $adminModule->sGetUserData()['additional']['user']['customergroup'];

        /** @var LoginHelperInterface $loginHelper */
        $loginHelper = $this->get('swag_business_essentials.login_helper');
        if (!$loginHelper->isLoginAllowed($currentCustomerGroup)) {
            if ($this->Response()->isRedirect()) {
                $this->clearRedirect();
            }

            $adminModule->logout();

            $this->Request()->setParam(
                'errorMessages',
                $this->get('snippets')->getNamespace('frontend/account/login')->get('PrivateLoginRegistrationUnlockError')
            );
            $this->indexAction();

            return;
        }
    }

    /**
     * Redirects the customer to the proper controller / action due to the given settings in the "loginRedirect"-config.
     */
    public function redirectLoginAction()
    {
        /** @var LoginHelperInterface $loginHelper */
        $loginHelper = $this->get('swag_business_essentials.login_helper');

        $loginUrl = $loginHelper->getRedirectUrl();

        $this->redirect($loginUrl);
    }

    /**
     * Redirects the user back to the register form with information about his confirmation status
     */
    public function confirmValidationAction()
    {
        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');

        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');

        $hash = $this->Request()->get('sConfirmation');

        $sql = "SELECT `data` FROM `s_core_optin` WHERE `hash` = ? AND type = 'swRegister'";
        $result = $connection->fetchColumn($sql, [$hash]);

        // Triggers an Error-Message, which tells the customer that his confirmation link was invalid
        if (empty($result)) {
            $this->indexAction();
            $this->view->assign('optinhashinvalid', true);

            return;
        }

        if (($data = unserialize($result)) === false || !isset($data['customerId'])) {
            throw new InvalidArgumentException(sprintf('The data for hash \'%s\' is corrupted.', $hash));
        }
        $customerId = (int) $data['customerId'];

        /** @var DateTime $date */
        $date = new DateTime();

        /** @var Customer $customer */
        $customer = $modelManager->find(Customer::class, $customerId);

        $customer->setFirstLogin($date);
        $customer->setDoubleOptinConfirmDate($date);
        $customer->setActive(true);

        $modelManager->persist($customer);
        $modelManager->flush();

        $sql = "DELETE FROM `s_core_optin` WHERE `hash` = ?  AND type = 'swRegister'";
        $connection->executeQuery($sql, [$this->Request()->get('sConfirmation')]);

        $this->indexAction();
        $this->view->assign('showOptinSuccess', true);
    }

    /**
     * Prevents a scheduled redirect
     */
    private function clearRedirect()
    {
        $response = $this->Response();
        $response->clearHeaders();
        $response->setHttpResponseCode(200);
    }

    /**
     * @return array
     */
    private function getCountries()
    {
        $context = $this->get('shopware_storefront.context_service')->getShopContext();
        if ($context === null) {
            return [];
        }

        /** @var LocationServiceInterface $service */
        $service = $this->get('shopware_storefront.location_service');
        $countries = $service->getCountries($context);

        return $this->get('legacy_struct_converter')->convertCountryStructList($countries);
    }

    /**
     * Returns the private shopping register-data from the view, if any available.
     *
     * @return array
     */
    private function getPrivateShoppingRegisterAssign()
    {
        /** @var Enlight_Components_Session_Namespace $session */
        $session = $this->get('session');
        $assign = $session->offsetGet('PrivateShoppingRegisterAssign');

        if (!$assign) {
            return [];
        }

        return $assign;
    }
}
