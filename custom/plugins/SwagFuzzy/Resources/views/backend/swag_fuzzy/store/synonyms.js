// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/synonyms"}
Ext.define('Shopware.apps.SwagFuzzy.store.Synonyms', {
    extend: 'Shopware.store.Association',

    configure: function () {
        return {
            controller: 'SwagFuzzySynonyms'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.Synonyms'
});
// {/block}
