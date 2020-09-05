// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/relevance"}
Ext.define('Shopware.apps.SwagFuzzy.store.Relevance', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'SwagFuzzyRelevance'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.Relevance'
});
// {/block}
