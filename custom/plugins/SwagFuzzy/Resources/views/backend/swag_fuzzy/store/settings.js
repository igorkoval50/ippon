// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/settings"}
Ext.define('Shopware.apps.SwagFuzzy.store.Settings', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'SwagFuzzySettings'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.Settings'
});
// {/block}
