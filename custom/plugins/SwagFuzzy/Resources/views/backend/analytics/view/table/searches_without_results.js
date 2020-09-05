
/**
 * Analytics SearchesWithoutResults Table
 *
 * @category   Shopware
 * @package    Analytics
 * @copyright  Copyright (c) shopware AG (http://www.shopware.de)
 *
 */
// {namespace name=backend/analytics/view/fuzzy}
// {block name="backend/analytics/view/table/Searches_without_results"}
Ext.define('Shopware.apps.Analytics.view.table.SearchesWithoutResults', {
    extend: 'Shopware.apps.Analytics.view.main.Table',
    alias: 'widget.analytics-table-searches_without_results',

    initComponent: function () {
        var me = this;

        me.columns = {
            items: me.getColumns(),
            defaults: {
                flex: 1,
                sortable: false
            }
        };

        me.callParent(arguments);
    },

    getColumns: function () {
        return [
            {
                dataIndex: 'searchTerm',
                text: '{s name=tableSearchesWithoutResults/searchTerm}Search term{/s}',
                renderer: function (value) {
                    return '<a href="{url module=frontend controller=search action=index}?sSearch=' +
                        value + '" target="_blank" ' +
                        'title="{s name=tableSearchesWithoutResults/linkToolTip}Open the search term in the frontend search{/s}">' +
                        value + '</a>';
                }
            },
            {
                dataIndex: 'lastSearchDate',
                text: '{s name=tableSearchesWithoutResults/lastSearchDate}Last searched on{/s}'
            },
            {
                dataIndex: 'currentCount',
                text: '{s name=tableSearchesWithoutResults/searchesCount}Searched in Period{/s}'
            },
            {
                dataIndex: 'searchesCount',
                text: '{s name=tableSearchesWithoutResults/searchesTotalCount}Total searches{/s}'
            },
            {
                dataIndex: 'shop',
                text: '{s name=tableSearchesWithoutResults/shop}Shop(s){/s}'
            }
        ];
    }
});
// {/block}
