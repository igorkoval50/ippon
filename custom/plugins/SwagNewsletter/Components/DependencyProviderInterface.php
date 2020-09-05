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

namespace SwagNewsletter\Components;

use Shopware\Models\Shop\Shop;

interface DependencyProviderInterface
{
    /**
     * Returns the module with the given name, if any exists.
     *
     * @param string $moduleName
     *
     * @return mixed
     */
    public function getModule($moduleName);

    /**
     * Checks if a shop instance exists.
     *
     * @return bool
     */
    public function hasShop();

    /**
     * Returns the currently active shop instance - if any given or active default.
     *
     * @return Shop
     */
    public function getShop();

    /**
     * @return \Enlight_Controller_Front
     */
    public function getFrontendController();

    /**
     * @return \Enlight_Components_Session_Namespace
     */
    public function getSession();

    /**
     * Checks if a parameter exists in the DI container
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter);

    /**
     * Gets a parameter from the DI container
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getParameter($parameter);
}
