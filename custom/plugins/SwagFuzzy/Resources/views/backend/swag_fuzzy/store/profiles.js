// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/profiles"}
Ext.define('Shopware.apps.SwagFuzzy.store.Profiles', {
    extend: 'Shopware.store.Listing',

    configure: function () {
        return {
            controller: 'SwagFuzzyProfiles'
        };
    },

    model: 'Shopware.apps.SwagFuzzy.model.Profiles'
});
// {/block}
