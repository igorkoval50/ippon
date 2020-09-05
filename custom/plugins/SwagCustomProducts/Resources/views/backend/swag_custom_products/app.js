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

// {block name="backend/swag_custom_products/app"}
Ext.define('Shopware.apps.SwagCustomProducts', {

    extend: 'Enlight.app.SubApplication',

    name: 'Shopware.apps.SwagCustomProducts',

    /**
     * Enable bulk loading
     * @boolean
     */
    bulkLoad: true,

    /**
     * Sets the loading path for the sub-application.
     *
     * @string
     */
    loadPath: '{url action="load"}',

    controllers: [
        'Main'
    ],

    views: [
        'overview.Window',
        'overview.List',

        'detail.Window',
        'detail.Detail',
        'detail.Article',
        'detail.OptionList',

        'option.Window',
        'option.Detail',

        'option.types.AbstractTypeContainer',

        'components.RowTextEditor'
    ],

    models: [
        'Template',
        'Article',
        'Option',
        'Value',
        'Price'
    ],

    stores: [
        'Template'
    ],

    launch: function () {
        var me = this,
            mainController = me.getController('Main');

        return mainController.mainWindow;
    }
});
// {/block}
