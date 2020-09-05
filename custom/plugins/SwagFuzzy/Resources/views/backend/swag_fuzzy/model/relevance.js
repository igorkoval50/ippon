// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/relevance"}
Ext.define('Shopware.apps.SwagFuzzy.model.Relevance', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagFuzzyRelevance',
            detail: 'Shopware.apps.SwagFuzzy.view.detail.relevance.Relevance'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' },
        { name: 'relevance', type: 'int' },
        { name: 'tableId', type: 'int' },
        { name: 'field', type: 'string' },
        { name: 'doNotSplit', type: 'boolean', defaultValue: false }
    ]
});
// {/block}
