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

use SwagCustomProducts\Structs\OptionStruct;

/**
 * This interface is for easy extending or overwriting the CustomProductService
 */
interface CustomProductsServiceInterface
{
    /**
     * Checks if the current sArticle is a CustomProduct
     *
     * Possible inputs:
     * A product order number
     *      $this->isCustomProduct($Product_ORDERNUMBER)
     *
     * A basket id with isBasketId = true
     *      $this->isCustomProduct($BasketId, true)
     *
     * @param string $identifier
     * @param bool   $isBasketId
     *
     * @return bool
     */
    public function isCustomProduct($identifier, $isBasketId = false);

    /**
     * Search for the option by the optionID
     *
     * @param int  $optionId
     * @param bool $basketCalculation
     *
     * @return array
     */
    public function getOptionById($optionId, array $configuration, $basketCalculation = false, array $basketPosition = []);

    /**
     * Reads all options by configuration
     *
     * @return OptionStruct[]
     */
    public function getOptionsByConfiguration(array $configuration);

    /**
     * Options could be required. This method checks for required options in the template
     *
     * @param int $productId
     *
     * @return bool
     */
    public function checkForRequiredOptions($productId);

    /**
     * Returns the selected / filled options due to the given hash.
     *
     * @param string $hash
     *
     * @return array
     */
    public function getOptionsFromHash($hash);
}
