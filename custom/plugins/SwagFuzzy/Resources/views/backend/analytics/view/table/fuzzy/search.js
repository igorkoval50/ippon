
// {namespace name=backend/analytics/view/fuzzy}
// {block name="backend/analytics/view/table/search"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.Analytics.view.table.fuzzy.Search', {
    override: 'Shopware.apps.Analytics.view.table.Search',

    getColumns: function () {
        var me = this,
            columns = me.callParent(arguments);

        Ext.each(columns, function (column) {
            if (column.dataIndex === 'searchterm') {
                column.renderer = me.linkRenderer;
            }
        });

        return columns;
    },

    /**
     * @param value
     * @param metaData
     * @param { Ext.Data.Model } record
     * @returns { string }
     */
    linkRenderer: function (value, metaData, record) {
        var me = this,
            shop = record.get('shop').split(',')[0],
            shopId = me.shopStore.findRecord('name', shop).get('id');

        return '<a href="{url module=frontend controller=search action=index}' +
                '?sSearch=' + value +
                '&__shop=' + shopId +
                '" target="_blank" ' +
                'title="{s name=tableSearchesWithoutResults/linkToolTip}Open the search term in the frontend search{/s}">' +
                value + '</a>';
    }
});
// {/block}
