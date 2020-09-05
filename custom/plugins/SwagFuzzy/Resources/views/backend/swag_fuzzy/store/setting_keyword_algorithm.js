// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/setting_search_algorithms"}
Ext.define('Shopware.apps.SwagFuzzy.store.SettingKeywordAlgorithm', {
    extend: 'Ext.data.Store',

    storeId: 'SwagFuzzySettingKeywordAlgorithm',

    fields: ['value', 'name', 'description'],
    data: [
        {
            value: 'soundex',
            name: '{s name=searchAlgorithms/useSoundex}Use Soundex for finding keywords{/s}',
            description: '{s name=searchAlgorithms/useSoundexHelpText}Fits best with English.{/s}'
        },
        {
            value: 'cologne-phonetic',
            name: '{s name=searchAlgorithms/useColognePhonetic}Use Cologne Phonetic for finding keywords{/s}',
            description: '{s name=searchAlgorithms/useColognePhoneticHelpText}Fits best with German.{/s}'
        },
        {
            value: 'metaphone',
            name: '{s name=searchAlgorithms/useMetaphone}Use Metaphone for finding keywords{/s}',
            description: '{s name=searchAlgorithms/useMetaphoneHelpText}Similar to Soundex, but more precise. Might be slower in certain circumstances.{/s}'
        }
    ]
});
// {/block}
