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

namespace SwagCustomProducts\Bootstrap;

/**
 * This class will be used to manage all custom schema adjustments which will be executed in the install and uninstall
 * method of this plugin. This class is used only to manage all classes and attributes at a central place.
 */
class SchemaManager
{
    /**
     * attributes for the Custom Products plugin
     *
     * @return array
     */
    public function getCustomProductsAttributes()
    {
        return [
            [
                'table' => 's_order_basket_attributes',
                'column' => 'swag_custom_products_configuration_hash',
                'type' => 'text',
                'default' => null,
            ],
            [
                'table' => 's_order_basket_attributes',
                'column' => 'swag_custom_products_once_price',
                'type' => 'boolean',
                'default' => 0,
            ],
            [
                'table' => 's_order_basket_attributes',
                'column' => 'swag_custom_products_mode',
                'type' => 'integer',
                'default' => 0,
            ],
            [
                'table' => 's_media_attributes',
                'column' => 'swag_custom_products_permanent',
                'type' => 'boolean',
                'default' => 0,
            ],
            [
                'table' => 's_order_details_attributes',
                'column' => 'swag_custom_products_mode',
                'type' => 'integer',
                'default' => 0,
            ],
            [
                'table' => 's_order_details_attributes',
                'column' => 'swag_custom_products_configuration_hash',
                'type' => 'text',
                'default' => null,
            ],
        ];
    }
}
