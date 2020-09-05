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

namespace SwagBusinessEssentials\Components\PrivateRegister;

use Shopware\Models\Shop\Shop;

interface RegistrationHelperInterface
{
    /**
     * Checks if the registration for a specific customer group and shop is allowed.
     *
     * @param string $customerGroupKey
     * @param int    $shopId
     *
     * @return bool
     */
    public function isRegistrationAllowed($customerGroupKey, $shopId);

    /**
     * Returns the template path if the configured template file exists.
     * Otherwise returns null.
     *
     * @param string $customerGroup
     *
     * @return string|null
     */
    public function getTemplate($customerGroup);

    /**
     * Returns the target-params to redirect a user properly after registering.
     *
     * @param string $customerGroup - This is not the current customer group, but the configured target customer group
     *                              of the current registration
     *
     * @return array
     */
    public function getTargetUrl($customerGroup, Shop $shop);

    /**
     * Registers and sets the theme for the current user.
     *
     * @param string $customerGroup
     */
    public function registerTheme($customerGroup, Shop $shop);

    /**
     * Checks if the user should see a confirmation message due to the settings.
     *
     * @param string $validationCustomerGroup
     *
     * @return bool
     */
    public function isConfirmationNeeded($validationCustomerGroup, Shop $shop);

    /**
     * Fetches the validation column of a user by its customer id given in the params.
     *
     * @param int $customerId
     *
     * @return string
     */
    public function getValidationCustomerGroup($customerId);

    /**
     * Builds a proper redirect url from the register-redirect configuration.
     *
     * @param string $customerGroup
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getRedirectUrl($customerGroup);

    /**
     * Reads the necessary register-data from the session.
     *
     * @param string $customerGroup
     * @param array  $register
     *
     * @return array
     */
    public function getRegisterDataFromAssign($customerGroup, $register);

    /**
     * Returns the "registergroup" config due to the given customer group.
     *
     * @param string $customerGroup
     *
     * @return string
     */
    public function getRegisterGroup($customerGroup);
}
