// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/main/search_tables"}
Ext.define('Shopware.apps.SwagFuzzy.view.main.SearchTables', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.swagFuzzy-main-searchTables',

    store: Ext.create('Shopware.apps.SwagFuzzy.store.SearchTables'),

    configure: function () {
        return {
            detailWindow: 'Shopware.apps.SwagFuzzy.view.detail.searchTable.Window',
            columns: {
                table: { header: '{s name=searchTables/tableColumn}Table{/s}' },
                referenceTable: { header: '{s name=searchTables/referenceTableColumn}Reference table{/s}' },
                foreignKey: { header: '{s name=searchTables/foreignKeyColumn}Foreign key{/s}' },
                additionalCondition: { header: '{s name=searchTables/additionalConditionColumn}Additional condition{/s}' }
            }
        };
    }
});
// {/block}
