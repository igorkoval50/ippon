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

// {block name="backend/swag_business_essentials/app"}
Ext.define('Shopware.apps.SwagBusinessEssentials', {
    extend: 'Enlight.app.SubApplication',
    name: 'Shopware.apps.SwagBusinessEssentials',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main', 'Configuration', 'BusinessEssentials', 'TemplateVariables' ],

    views: [
        'main.Window',
        'list.BusinessEssentials',
        'template_variables.Grid',
        'template_variables.Window',
        'template_variables.Detail',
        'list.FilterPanel',
        'private_register.Detail',
        'private_shopping.Detail',
        'template_variables.CustomerGroups',
        'components.InfoContainer',
        'components.GridField',
        'components.ParamsField',
        'components.ParamsWindow'
    ],

    models: [
        'BusinessEssentials',
        'TemplateVariables',
        'CustomerGroup',
        'PrivateRegister',
        'PrivateShopping',
        'Mails',
        'Controllers',
        'Params'
    ],

    stores: [
        'BusinessEssentials',
        'TemplateVariables',
        'Mails',
        'RegisterTemplates',
        'Controllers'
    ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});
// {/block}
