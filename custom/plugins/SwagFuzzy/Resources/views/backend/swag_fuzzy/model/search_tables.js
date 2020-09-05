// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/search_tables"}
Ext.define('Shopware.apps.SwagFuzzy.model.SearchTables', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagFuzzySearchTables',
            detail: 'Shopware.apps.SwagFuzzy.view.detail.searchTable.SearchTable'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'table', type: 'string' },
        { name: 'referenceTable', type: 'string' },
        { name: 'foreignKey', type: 'string' },
        { name: 'additionalCondition', type: 'string' }
    ]
});
// {/block}
