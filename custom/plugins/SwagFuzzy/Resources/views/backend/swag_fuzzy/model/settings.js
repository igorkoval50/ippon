// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/settings"}
Ext.define('Shopware.apps.SwagFuzzy.model.Settings', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagFuzzySettings',
            detail: 'Shopware.apps.SwagFuzzy.view.main.Settings'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'keywordAlgorithm', type: 'string' },
        { name: 'exactMatchAlgorithm', type: 'string' },
        { name: 'searchDistance', type: 'int' },
        { name: 'searchExactMatchFactor', type: 'int' },
        { name: 'searchMatchFactor', type: 'int' },
        { name: 'searchMinDistancesTop', type: 'int' },
        { name: 'searchPartNameDistances', type: 'int' },
        { name: 'searchPatternMatchFactor', type: 'int' },
        { name: 'maxKeywordsAndSimilarWords', type: 'int' },
        { name: 'topSellerRelevance', type: 'int' },
        { name: 'newArticleRelevance', type: 'int' }
    ]
});
// {/block}
