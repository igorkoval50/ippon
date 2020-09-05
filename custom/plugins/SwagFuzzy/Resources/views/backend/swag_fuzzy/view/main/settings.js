// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/main/settings"}
Ext.define('Shopware.apps.SwagFuzzy.view.main.Settings', {
    extend: 'Shopware.model.Container',
    alias: 'widget.swagFuzzy-main-settings',

    record: Ext.create('Shopware.apps.SwagFuzzy.model.Settings'),

    padding: 10,

    style: {
        background: '#EBEDEF'
    },

    configure: function () {
        var me = this;

        return {
            controller: 'SwagFuzzySettings',
            fieldSets: [
                {
                    title: '{s name=settings/searchAlgorithms}Search algorithms{/s}',
                    fields: {
                        keywordAlgorithm: {
                            fieldLabel: '{s name=searchAlgorithms/keywordAlgorithm}Keyword algorithm{/s}',
                            xtype: 'combo',
                            store: me.getKeywordAlgorithmStore(),
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            helpText: me.getKeywordAlgorithmHelpText()
                        },
                        exactMatchAlgorithm: {
                            fieldLabel: '{s name=searchAlgorithms/exactMatchAlgorithm}Exact match algorithm{/s}',
                            xtype: 'combo',
                            store: me.getExactMatchAlgorithmStore(),
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            helpText: me.getExactMatchHelpText()
                        }
                    }
                },
                {
                    title: '{s name=settings/other}Other settings{/s}',
                    fields: {
                        searchDistance: {
                            fieldLabel: '{s name=other/searchDistance}Maximum distance allowed for string matching (%){/s}',
                            labelWidth: 250
                        },
                        searchExactMatchFactor: {
                            fieldLabel: '{s name=other/searchExactMatchFactor}Factor for accurate hits{/s}',
                            labelWidth: 250
                        },
                        searchMatchFactor: {
                            fieldLabel: '{s name=other/searchMatchFactor}Factor for short partial hits{/s}',
                            labelWidth: 250
                        },
                        searchMinDistancesTop: {
                            fieldLabel: '{s name=other/searchMinDistancesTop}Minimum relevance for top items (%){/s}',
                            labelWidth: 250
                        },
                        searchPartNameDistances: {
                            fieldLabel: '{s name=other/searchPartNameDistances}Maximum distance allowed for partial names (%){/s}',
                            labelWidth: 250
                        },
                        searchPatternMatchFactor: {
                            fieldLabel: '{s name=other/searchPatternMatchFactor}Factor for partial hits{/s}',
                            labelWidth: 250
                        },
                        maxKeywordsAndSimilarWords: {
                            fieldLabel: '{s name=other/maxKeywordsAndSimilarWords}Number of max. similar results shown in the frontend and used keywords for the search{/s}',
                            labelWidth: 250
                        }
                    }
                },
                {
                    title: '{s name=settings/additionalRelevance}Additional relevance{/s}',
                    fields: {
                        topSellerRelevance: {
                            fieldLabel: '{s name=additionalRelevance/topSellerRelevance}Relevance for top seller articles{/s}',
                            labelWidth: 250
                        },
                        newArticleRelevance: {
                            fieldLabel: '{s name=additionalRelevance/newArticleRelevance}Relevance for new articles{/s}',
                            labelWidth: 250
                        }
                    }
                }

            ]
        };
    },

    initComponent: function () {
        var me = this;

        me.callParent(arguments);
        me.title = '{s name=window/tab/settings}Default settings{/s}';
    },

    getKeywordAlgorithmStore: function () {
        var store = Ext.data.StoreManager.get('SwagFuzzySettingKeywordAlgorithm');

        if (!store) {
            store = Ext.create('Shopware.apps.SwagFuzzy.store.SettingKeywordAlgorithm');
        }

        return store;
    },

    getExactMatchAlgorithmStore: function () {
        var store = Ext.data.StoreManager.get('SwagFuzzySettingExactMatchAlgorithm');

        if (!store) {
            store = Ext.create('Shopware.apps.SwagFuzzy.store.SettingExactMatchAlgorithm');
        }

        return store;
    },

    getKeywordAlgorithmHelpText: function () {
        return this.getHelpTextFromStore(
            this.getKeywordAlgorithmStore()
        );
    },

    getExactMatchHelpText: function () {
        return this.getHelpTextFromStore(
            this.getExactMatchAlgorithmStore()
        );
    },

    getHelpTextFromStore: function (store) {
        var ret = '';

        store.each(function () {
            ret += '<h3>' + this.get('name') + '</h3><p>' + this.get('description') + '</p>';
        });

        return ret;
    }
});
// {/block}
