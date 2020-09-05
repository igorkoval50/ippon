
/**
 * Analytics Searches Without Results Store
 *
 * @category   Shopware
 * @package    Analytics
 * @copyright  Copyright (c) shopware AG (http://www.shopware.de)
 *
 */

// {namespace name=backend/analytics/view/fuzzy}
// {block name="backend/analytics/store/navigation/searches_without_results"}
Ext.define('Shopware.apps.Analytics.store.navigation.SearchesWithoutResults', {
    extend: 'Ext.data.Store',
    alias: 'widget.analytics-store-navigation-searches_without_results',
    fields: [
        'searchTerm',
        'lastSearchDate',
        'searchesCount',
        'currentCount',
        'shop'
    ],
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: '{url controller=SwagFuzzyAnalytics action=getSearchesWithoutResults}',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
// {/block}
