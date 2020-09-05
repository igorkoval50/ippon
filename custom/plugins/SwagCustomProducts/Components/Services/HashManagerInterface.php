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

namespace SwagCustomProducts\Components\Services;

/**
 * This interface is for easy extending or overwriting the HashManager
 */
interface HashManagerInterface
{
    /**
     * Returns the configuration hash. If no hash exists, it creates a new one
     *
     * @param bool $permanent
     *
     * @return string
     */
    public function manageHashByConfiguration(array $configuration, $permanent = false, array $options = []);

    /**
     * Searchs in the s_plugin_custom_products_configuration_hash table for the given hash
     * and returns the associated configuration
     *
     * @param string $configurationHash
     *
     * @return array
     */
    public function findConfigurationByHash($configurationHash);

    /**
     * Create a new md5 hash from the given configuration
     *
     * @return string
     */
    public function createHash(array $configuration);
}
