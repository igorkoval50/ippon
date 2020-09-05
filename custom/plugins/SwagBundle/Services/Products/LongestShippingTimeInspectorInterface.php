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

namespace SwagBundle\Services\Products;

use SwagBundle\Models\Bundle;

interface LongestShippingTimeInspectorInterface
{
    /**
     * Main entry point - cascades from worst to best case
     *
     * Tip: The number of cases in the template files should match the number of cases presented here.
     */
    public function determineLongestShippingProduct(array $products, Bundle $bundle);

    /**
     * find and set a not available article
     *
     * @return bool success
     */
    public function determineProductByNonAvailability(array $products);

    /**
     * find and set a not stocked product
     *
     * @return true success
     */
    public function determineProductByNonStocked(array $products);

    /**
     * find and set a stocked product
     *
     * @return bool success
     */
    public function determineProductByStocked(array $products);

    /**
     * find and set an esd product
     *
     * @return bool success
     */
    public function determineProductByEsd(array $products);
}
