// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/synonym_groups"}
Ext.define('Shopware.apps.SwagFuzzy.store.SynonymGroups', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'SwagFuzzySynonyms'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.SynonymGroups'
});
// {/block}
