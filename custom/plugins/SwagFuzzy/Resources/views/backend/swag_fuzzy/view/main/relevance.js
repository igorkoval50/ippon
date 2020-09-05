// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/main/relevance"}
Ext.define('Shopware.apps.SwagFuzzy.view.main.Relevance', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.swagFuzzy-main-relevance',

    store: Ext.create('Shopware.apps.SwagFuzzy.store.Relevance'),

    initComponent: function () {
        var me = this;

        me.tablesStore = Ext.create('Shopware.apps.SwagFuzzy.store.SearchTables');
        me.tablesStore.load();

        me.callParent(arguments);

        me.on('beforeshow', Ext.bind(me.onBeforeShowRelevanceTab, me));
    },

    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.SwagFuzzy.view.detail.relevance.Window',
            columns: {
                name: { header: '{s name=relevance/relevanceNameColumn}Name{/s}' },
                relevance: { header: '{s name=relevance/relevanceNumberColumn}Relevance{/s}' },
                tableId: {
                    header: '{s name=relevance/relevanceTableColumn}Table{/s}',
                    renderer: me.setTableName,
                    align: 'left'
                },
                field: { header: '{s name=relevance/relevanceFieldColumn}Field{/s}' },
                doNotSplit: { header: '{s name=search/detail/do_no_split_text namespace=backend/config/view/search}Do not split{/s}' }
            }
        };
    },

    setTableName: function (tableId) {
        var me = this,
            table = me.tablesStore.findRecord('id', tableId);

        return table.get('table');
    },

    /**
     * This is a fix for the following edge case:
     * Add a new table on the searchTablesTab, change to relevanceTab and add a new field which contains the new table.
     * For this case we need to reload the tableStore.
     */
    onBeforeShowRelevanceTab: function () {
        var me = this;

        me.tablesStore.reload();
    }
});
// {/block}
