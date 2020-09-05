// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/preview"}
Ext.define('Shopware.apps.SwagFuzzy.model.Preview', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagFuzzyPreview',
            detail: 'Shopware.apps.SwagFuzzy.view.detail.preview.Preview'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'number', type: 'string' },
        { name: 'name', type: 'string' },
        { name: 'ranking', type: 'int', mapping: 'attributes.search.ranking' },
        { name: 'keywords', type: 'string', mapping: 'attributes.search.keywords' },
        { name: 'relevances', type: 'string', mapping: 'attributes.search.relevances' },
        { name: 'isTopSeller', type: 'bool', mapping: 'attributes.search.isTopSeller' },
        { name: 'isNew', type: 'bool', mapping: 'attributes.marketing.isNew' }

    ]
});
// {/block}
