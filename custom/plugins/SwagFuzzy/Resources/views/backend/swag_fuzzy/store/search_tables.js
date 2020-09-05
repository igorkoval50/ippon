// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/search_tables"}
Ext.define('Shopware.apps.SwagFuzzy.store.SearchTables', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'SwagFuzzySearchTables'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.SearchTables'
});
// {/block}
