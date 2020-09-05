// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/synonyms"}
Ext.define('Shopware.apps.SwagFuzzy.model.Synonyms', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            listing: 'Shopware.apps.SwagFuzzy.view.detail.synonymGroup.Synonyms'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'synonymGroupId', type: 'int' },
        { name: 'name', type: 'string' }
    ]
});
// {/block}
