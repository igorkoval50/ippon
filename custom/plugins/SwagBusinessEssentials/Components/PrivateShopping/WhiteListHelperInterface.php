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
 * Interface WhiteListHelperInterface
 */
interface WhiteListHelperInterface
{
    /**
     * Checks if a controller is white-listed.
     *
     * @param string $customerGroup
     * @param string $controllerName
     * @param string $actionName
     *
     * @return
     */
    public function isControllerWhiteListed($customerGroup, $controllerName, $actionName);

    /**
     * Returns an array of all available frontend controllers, prepared for a backend store.
     *
     * @return array
     */
    public function getControllers();

    /**
     * Prepares the white-list string for a backend store.
     * Splits a string like 'custom,index' into an array and adds a simple name to it.
     *
     * @param string $controllerString
     *
     * @return array
     */
    public function prepareWhiteList($controllerString);

    /**
     * Converts an array of controllers back to a string.
     * This is necessary to stay compatible with older versions of the plugin.
     *
     * @param array $controllers
     *
     * @return string
     */
    public function convertToString($controllers);
}
