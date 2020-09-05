// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/detail/synonym_group/synonym_group"}
Ext.define('Shopware.apps.SwagFuzzy.view.detail.synonymGroup.SynonymGroup', {
    extend: 'Shopware.model.Container',
    alias: 'widget.synonymGroup-detail-container',
    padding: '10 20 10 10',

    configure: function () {
        var emotionStore = Ext.create('Shopware.apps.SwagFuzzy.store.Emotions'),
            emotionModel = Ext.create('Shopware.apps.SwagFuzzy.model.Emotions');

        emotionModel.set('id', -1);
        emotionModel.set('name', '{s name=synonymGroupDetail/noShoppingWorld}Don´t use a shopping world{/s}');

        emotionStore.load({
            callback: function () {
                emotionStore.insert(0, emotionModel);
            }
        });

        return {
            controller: 'SwagFuzzySynonyms',
            associations: ['synonyms'],
            splitFields: false,
            fieldSets: [
                {
                    title: '{s name=synonymGroupDetail/generalFieldSet}General settings{/s}',
                    fields: {
                        groupName: {
                            fieldLabel: '{s name=synonymGroups/groupNameColumn}Group name{/s}',
                            allowBlank: false
                        },
                        shopId: {
                            fieldLabel: '{s name=synonymGroupDetail/shop}Shop{/s}',
                            allowBlank: false
                        },
                        active: {
                            fieldLabel: '{s name=synonymGroups/activeColumn}Active{/s}',
                            allowBlank: false
                        }
                    }
                },
                {
                    title: '{s name=synonymGroupDetail/normalFieldSet}Settings for search page{/s}',
                    fields: {
                        normalSearchEmotionId: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchEmotionId}Shopping world for the search page{/s}',
                            xtype: 'combobox',
                            store: emotionStore,
                            queryMode: 'local',
                            valueField: 'id',
                            displayField: 'name',
                            forceSelection: true,
                            pageSize: 25
                        },
                        normalSearchBanner: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchBanner}Banner for the search page{/s}',
                            albumId: [-1, -2, -3, -11]
                        },
                        normalSearchLink: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchLink}Link for the banner to a specific page{/s}'
                        },
                        normalSearchHeader: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchHeader}Headline for the banner{/s}',
                            xtype: 'tinymce'
                        },
                        normalSearchDescription: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchDescription}Description for the banner{/s}',
                            xtype: 'tinymce'
                        }
                    }
                },
                {
                    title: '{s name=synonymGroupDetail/ajaxFieldSet}Settings for ajax search{/s}',
                    fields: {
                        ajaxSearchBanner: {
                            fieldLabel: '{s name=synonymGroupDetail/ajaxSearchBanner}Banner for the ajax search{/s}',
                            albumId: [-1, -2, -3, -11]
                        },
                        ajaxSearchLink: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchLink}Link for the banner to a specific page{/s}'
                        },
                        ajaxSearchHeader: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchHeader}Headline for the banner{/s}',
                            xtype: 'tinymce'
                        },
                        ajaxSearchDescription: {
                            fieldLabel: '{s name=synonymGroupDetail/normalSearchDescription}Description for the banner{/s}',
                            xtype: 'tinymce'
                        }
                    }
                }
            ]
        };
    }
});
// {/block}
