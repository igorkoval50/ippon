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

use Shopware\Models\Media\Media;

/**
 * This interface is for easy extending or overwriting the TemplateService
 */
interface TemplateServiceInterface
{
    /**
     * Returns the CustomProduct template by the given product id.
     * the $enrichTemplate parameter is for enrich the template with options, values and prices
     *
     * @param int   $productId
     * @param bool  $enrichTemplate
     * @param float $productPrice
     *
     * @return array|null
     */
    public function getTemplateByProductId($productId, $enrichTemplate = true, $productPrice = 0.00);

    /**
     * Enrich the CustomProduct template with extended data like prices
     *
     * @param float      $productPrice
     * @param string|int $customerGroupId
     * @param string|int $fallbackId
     * @param Media[]    $medias
     *
     * @return array
     */
    public function enrichValues(
        array $values,
        array $valuePrices,
        $productPrice = 0.00,
        $customerGroupId,
        $fallbackId,
        array $medias
    );

    /**
     * Enrich the CustomProduct options with extended data like prices
     *
     * @param float      $productPrice
     * @param string|int $customerGroupId
     * @param string|int $fallbackId
     *
     * @return array
     */
    public function enrichOptions(
        array $options,
        array $values,
        array $optionPrices,
        $productPrice = 0.00,
        $customerGroupId,
        $fallbackId
    );

    /**
     * Checks if the internal name is already used and returns the result as boolean
     *
     * @param string $internalName
     *
     * @return bool
     */
    public function isInternalNameAssigned($internalName);

    /**
     * Returns all CustomProduct options by the CustomProduct templateId
     *
     * @param int $templateId
     *
     * @return array
     */
    public function getOptionsByTemplateId($templateId);

    /**
     * Reads the prices by optionId OR by valueId.
     * it is important to make only one indication!
     *
     * @param int|null $optionId
     * @param int|null $valueId
     *
     * @return array
     */
    public function getPrices($optionId = null, $valueId = null);

    /**
     * Returns the CustomProduct option with the given optionId
     * The parameter $basketCalculation is for calculate the gross or net price
     *
     * @param int   $id
     * @param float $productPrice
     * @param bool  $basketCalculation
     *
     * @return array|null
     */
    public function getOptionById($id, $productPrice = 0.00, $basketCalculation = false);

    /**
     * Returns a CustomProduct value by the given valueId
     * The parameter $basketCalculation is for calculate the gross or net price
     *
     * @param int   $id
     * @param float $productPrice
     * @param bool  $basketCalculation
     *
     * @return array|null
     */
    public function getValueById($id, $productPrice = 0.00, $basketCalculation = false);

    /**
     * Enrich objects with prices and tax Ids
     * The parameter $basketCalculation is for calculate the gross or net price
     *
     * @param float $productPrice
     * @param bool  $basketCalculation
     *
     * @return array
     */
    public function enrich(array $data, $productPrice = 0.00, $basketCalculation = false);

    /**
     * Enrich the template with all required data like medias, options, values and prices
     *
     * @param int|string $templateId
     * @param float      $productPrice
     *
     * @return array
     */
    public function enrichTemplate(array $template, $templateId, $productPrice = 0.00);
}
