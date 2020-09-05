// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/setting_search_logic"}
Ext.define('Shopware.apps.SwagFuzzy.store.SettingExactMatchAlgorithm', {
    extend: 'Ext.data.Store',

    storeId: 'SwagFuzzySettingExactMatchAlgorithm',

    fields: ['value', 'name', 'description'],
    data: [
        {
            value: 'levenshtein',
            name: '{s name=searchAlgorithms/useLevenshtein}Use Levenshtein for more accurate hits{/s}',
            description: '{s name=searchAlgorithms/useLevenshteinHelpText}Calculates the Levenshtein distance between two words which is defined as the minimal number of characters you have to replace, insert or delete.{/s}'
        },
        {
            value: 'similar-text',
            name: '{s name=searchAlgorithms/useSimilarText}Use SimilarText for more accurate hits{/s}',
            description: '{s name=searchAlgorithms/useSimilarTextHelpText}Calculates the similarity between two words.{/s}'
        }
    ]
});
// {/block}
