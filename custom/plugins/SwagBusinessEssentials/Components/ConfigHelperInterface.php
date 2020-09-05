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

namespace SwagBusinessEssentials\Components;

/**
 * Interface ConfigHelperInterface
 */
interface ConfigHelperInterface
{
    const PRIVATE_REGISTER_TABLE = 's_core_plugins_b2b_cgsettings';
    const PRIVATE_SHOPPING_TABLE = 's_core_plugins_b2b_private';

    /**
     * Reads the config $configName by the given table-name for a customer group.
     *
     * @param string $table
     * @param string $configName
     * @param string $customerGroup
     *
     * @return string|bool
     */
    public function getConfig($table, $configName, $customerGroup);
}
