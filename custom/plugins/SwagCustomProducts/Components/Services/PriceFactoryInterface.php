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

use SwagCustomProducts\Models\Price;

/**
 * This interface is for easy extending or overwriting the PriceFactory
 */
interface PriceFactoryInterface
{
    /**
     * Creates a new price array with the default settings
     *
     * @param int|null $optionId
     * @param int|null $valueId
     *
     * @return Price[]
     */
    public function createDefaultPrice($optionId = null, $valueId = null);

    /**
     * Creates a price array with the given charges. Add an option OR a value id to create an option price OR a value price
     *
     * @param int|null $optionId
     * @param int|null $valueId
     *
     * @return Price[]
     */
    public function createPricesFromCharge(array $charge, $optionId = null, $valueId = null);
}
