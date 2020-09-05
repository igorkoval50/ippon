// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/preview"}
Ext.define('Shopware.apps.SwagFuzzy.store.Preview', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'SwagFuzzyPreview'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.Preview'
});
// {/block}
