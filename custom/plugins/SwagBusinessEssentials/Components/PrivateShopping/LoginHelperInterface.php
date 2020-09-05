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

namespace SwagBusinessEssentials\Components\PrivateShopping;

/**
 * Interface LoginHelperInterface
 */
interface LoginHelperInterface
{
    /**
     * Returns all necessary view-variables.
     *
     * @return array
     */
    public function getViewVariables();

    /**
     * Returns the login template name to be loaded for the private shopping login.
     *
     * @return string
     */
    public function getLoginTpl();

    /**
     * Checks if the login is allowed to display a proper error.
     *
     * @param $currentCustomerGroup
     *
     * @return bool
     */
    public function isLoginAllowed($currentCustomerGroup);

    /**
     * Returns the url for the login-panel.
     * Will always create a link redirecting to the "redirectLogin"-action.
     *
     * @return string
     */
    public function getLoginUrl();

    /**
     * Returns the url to be redirected to, due to the "loginRedirect"-settings.
     *
     * @return string
     */
    public function getRedirectUrl();
}
