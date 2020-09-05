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

// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/application"}
Ext.define('Shopware.apps.SwagFuzzy', {
    extend: 'Enlight.app.SubApplication',

    name: 'Shopware.apps.SwagFuzzy',

    loadPath: '{url action=load}',

    bulkLoad: true,

    controllers: [
        'Main'
    ],

    views: [
        'main.Preview',
        'main.Profiles',
        'main.Relevance',
        'main.SearchTables',
        'main.Settings',
        'main.SynonymGroups',
        'main.Window',
        'detail.profile.Profile',
        'detail.profile.Window',
        'detail.relevance.Relevance',
        'detail.relevance.Window',
        'detail.searchTable.SearchTable',
        'detail.searchTable.Window',
        'detail.synonymGroup.SynonymGroup',
        'detail.synonymGroup.Synonyms',
        'detail.synonymGroup.Window'
    ],

    models: [
        'Preview',
        'Emotions',
        'Profiles',
        'Relevance',
        'SearchTables',
        'Settings',
        'SynonymGroups',
        'Synonyms',
        'TableColumns'
    ],

    stores: [
        'Preview',
        'Emotions',
        'Profiles',
        'Relevance',
        'SearchTables',
        'Settings',
        'SettingKeywordAlgorithm',
        'SettingExactMatchAlgorithm',
        'SynonymGroups',
        'Synonyms',
        'TableColumns'
    ],

    launch: function () {
        return this.getController('Main').mainWindow;
    }
});
// {/block}
