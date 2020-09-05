// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/profiles"}
Ext.define('Shopware.apps.SwagFuzzy.model.Profiles', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagFuzzyProfiles',
            detail: 'Shopware.apps.SwagFuzzy.view.detail.profile.Profile'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' },
        { name: 'standard', type: 'boolean' },
        { name: 'settings', type: 'string' },
        { name: 'relevance', type: 'string' },
        { name: 'searchTables', type: 'string' }
    ]
});
// {/block}
