// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/emotions"}
Ext.define('Shopware.apps.SwagFuzzy.model.Emotions', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagFuzzyEmotions'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' }
    ]
});
// {/block}
