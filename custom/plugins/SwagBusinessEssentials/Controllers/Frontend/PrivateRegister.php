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

use SwagBusinessEssentials\Components\PrivateRegister\RegistrationHelperInterface;

class Shopware_Controllers_Frontend_PrivateRegister extends Enlight_Controller_Action
{
    /**
     * Redirect to the default register controller.
     * Necessary for compatibility reasons.
     */
    public function indexAction()
    {
        $params = $this->Request()->getParams();

        $params['controller'] = 'register';

        $this->redirect($params);
    }

    /**
     * Redirects the customer to the proper controller / action due to the given settings in the "registerRedirect"-config.
     */
    public function registerRedirectAction()
    {
        /** @var RegistrationHelperInterface $registrationHelper */
        $registrationHelper = $this->get('swag_business_essentials.registration_helper');
        $customerGroup = $this->get('modules')->Admin()->sGetUserData()['additional']['user']['customergroup'];

        $redirectUrl = $registrationHelper->getRedirectUrl($customerGroup);

        $this->Response()->setRedirect($redirectUrl);
    }

    /**
     * Redirects the user to the index page with an additional parameter to show a confirmation message
     */
    public function registerConfirmAction()
    {
        $this->forward('index', 'PrivateLogin', 'frontend', ['showConfirmation' => true]);
    }
}
