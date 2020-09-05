// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/emotions"}
Ext.define('Shopware.apps.SwagFuzzy.store.Emotions', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'SwagFuzzyEmotions'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.Emotions'
});
// {/block}
