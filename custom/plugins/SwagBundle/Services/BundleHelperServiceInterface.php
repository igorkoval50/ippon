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

namespace SwagBundle\Services;

use SwagBundle\Models\Price;

interface BundleHelperServiceInterface
{
    const GROUP_PREFIX = 'group-';

    const GROUP_PREFIX_REGEX = '/^(group-(\d)*::(\d)*::)/';

    /**
     * @param int    $bundleType
     * @param string $key
     *
     * @return bool
     */
    public function canConfigBeAdded($bundleType, array $selection, $key);

    /**
     * @param int $bundleProductID
     *
     * @return bool
     */
    public function isBundleProductInSelection($bundleProductID, array $selection);

    /**
     * @param $key
     *
     * @return bool
     */
    public function isConfigParameter($key);

    /**
     * @param string $key
     *
     * @return int
     */
    public function prepareArrayKey($key);

    /**
     * @param string $key
     *
     * @return int
     */
    public function getBundleProductId($key);

    /**
     * @param string $key
     *
     * @return string
     */
    public function getProductId($key);

    /**
     * @param bool $isTaxFree
     *
     * @return array
     */
    public function createPrice($isTaxFree, Price $price, array $discount);
}
