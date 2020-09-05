// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/controller/main"}
Ext.define('Shopware.apps.SwagFuzzy.controller.Main', {
    extend: 'Enlight.app.Controller',

    refs: [
        { ref: 'fuzzyFormPanel', selector: 'swagFuzzy-main-window form[name=swagFuzzy-form-panel]' },
        { ref: 'shopCombo', selector: 'swagFuzzy-main-window field[name=shop-combo]' },
        { ref: 'synonymGroupGrid', selector: 'swagFuzzy-main-synonymGroups' },
        { ref: 'synonymGroupDetail', selector: 'synonymGroup-detail-container' },
        { ref: 'relevanceGrid', selector: 'swagFuzzy-main-relevance' },
        { ref: 'searchTablesGrid', selector: 'swagFuzzy-main-searchTables' },
        { ref: 'profileGrid', selector: 'swagFuzzy-main-profiles' },
        { ref: 'previewGrid', selector: 'swagFuzzy-main-preview' },
        { ref: 'settingsContainer', selector: 'swagFuzzy-main-settings' }
    ],

    init: function () {
        var me = this;

        me.mainWindow = me.getView('main.Window').create({}).show();

        me.onChangeShop();

        me.control({
            'swagFuzzy-main-window': {
                changeShop: me.onChangeShop,
                saveSwagFuzzy: me.onSaveSwagFuzzy
            },
            'swagFuzzy-main-synonymGroups': {
                'duplicate-synonymGroup-item': me.onDuplicateSynonymGroup
            },
            'swagFuzzy-main-profiles': {
                'apply-profile': me.onApplyProfile
            }
        });

        Shopware.app.Application.addListener({
            'profiles-before-send-save-request': me.onBeforeSaveProfile,
            scope: me
        });

        Shopware.app.Application.addListener({
            'synonymgroups-before-add-item': me.onBeforeAddSynonymGroup,
            scope: me
        });
    },

    onChangeShop: function () {
        var me = this,
            shopStore = me.getShopCombo().store,
            shopId = me.getShopCombo().getValue();

        if (shopStore.isLoading() && !shopId) {
            shopStore.load({
                callback: function (records) {
                    shopId = records[0].get('id');
                    me.setShopId(shopId);
                }
            });
        } else {
            me.setShopId(shopId);
        }
    },

    setShopId: function (shopId) {
        var me = this,
            settingsContainer = me.getSettingsContainer(),
            synonymGroupStore = me.getSynonymGroupGrid().getStore(),
            relevanceStore = me.getRelevanceGrid().getStore(),
            searchTablesStore = me.getSearchTablesGrid().getStore(),
            profileStore = me.getProfileGrid().getStore(),
            previewStore = me.getPreviewGrid().getStore(),
            settingsStore = Ext.create('Shopware.apps.SwagFuzzy.store.Settings');

        synonymGroupStore.getProxy().extraParams.shopId = shopId;
        synonymGroupStore.load();

        relevanceStore.load();

        searchTablesStore.load();

        profileStore.load();

        previewStore.getProxy().extraParams.shopId = shopId;
        previewStore.load();

        settingsStore.getProxy().extraParams.shopId = shopId;
        settingsContainer.setLoading(true);
        settingsStore.load({
            callback: function (record) {
                if (record[0]) {
                    me.getFuzzyFormPanel().loadRecord(record[0]);
                } else {
                    record = Ext.create('Shopware.apps.SwagFuzzy.model.Settings');
                    record.set('shopId', shopId);
                    me.getFuzzyFormPanel().loadRecord(record);
                }
                settingsContainer.setLoading(false);
            }
        });
    },

    onSaveSwagFuzzy: function () {
        var me = this,
            view = me.getFuzzyFormPanel(),
            form = view.getForm(),
            record = form.getRecord();

        form.updateRecord(record);

        view.setLoading(true);
        record.save({
            callback: function () {
                view.setLoading(false);
                Shopware.Notification.createGrowlMessage('{s name=controller/onSaveTitle}Saved{/s}', '{s name=controller/onSaveText}Configuration was saved.{/s}');
            }
        });
    },

    onDuplicateSynonymGroup: function (grid, view, record) {
        grid.setLoading(true);
        Ext.Ajax.request({
            url: '{url controller="SwagFuzzySynonyms" action="cloneSynonymGroup"}',
            method: 'POST',
            params: {
                synonymGroupId: record.get('id')
            },
            success: function (response) {
                var responseText = Ext.decode(response.responseText),
                    synonymGroupStore = grid.getStore(),
                    synonymGroupId;

                if (responseText.success) {
                    synonymGroupId = responseText.synonymGroupId;
                    synonymGroupStore.load({
                        callback: function () {
                            var newRecord = synonymGroupStore.getById(synonymGroupId);

                            grid.fireEvent(grid.eventAlias + '-edit-item', grid, newRecord);
                        }
                    });
                } else {
                    Shopware.Notification.createGrowlMessage('Error!', responseText.message);
                }
                grid.setLoading(false);
            }
        });
    },

    onApplyProfile: function (grid, view, record) {
        var me = this,
            fuzzyFormPanel = me.getFuzzyFormPanel(),
            settingsRecord = fuzzyFormPanel.getForm().getRecord(),
            decodedSettings = Ext.decode(record.get('settings')),
            relevanceStore = me.getRelevanceGrid().getStore(),
            relevanceRecords = relevanceStore.data.items,
            decodedRelevance,
            newRelevance,
            searchTablesStore = me.getSearchTablesGrid().getStore(),
            searchTablesRecords = searchTablesStore.data.items,
            decodedSearchTables,
            newSearchTable;

        Ext.merge(settingsRecord.data, decodedSettings);

        // decode relevance and search tables records
        decodedRelevance = me.decodeRecords(record.get('relevance'));
        decodedSearchTables = me.decodeRecords(record.get('searchTables'));

        // if there is already a record with the same name/table, merge them
        decodedRelevance = me.mergeRecords(relevanceRecords, decodedRelevance, 'name');
        decodedSearchTables = me.mergeRecords(searchTablesRecords, decodedSearchTables, 'table');

        // if there is no record, create it
        if (decodedRelevance.length > 0) {
            Ext.each(decodedRelevance, function (relevance) {
                if (relevance) {
                    newRelevance = Ext.create('Shopware.apps.SwagFuzzy.model.Relevance', relevance);
                    relevanceStore.add(newRelevance);
                    newRelevance.save({
                        callback: function (record, operation) {
                            var response = Ext.decode(operation.response.responseText);
                            if (response.error) {
                                me.showErrorMessage(record);
                            }
                        }
                    });
                }
            });
        }

        if (decodedSearchTables.length > 0) {
            Ext.each(decodedSearchTables, function (searchTable) {
                if (searchTable) {
                    newSearchTable = Ext.create('Shopware.apps.SwagFuzzy.model.SearchTables', searchTable);
                    searchTablesStore.add(newSearchTable);
                    newSearchTable.save();
                }
            });
        }

        fuzzyFormPanel.setLoading(true);
        settingsRecord.save({
            callback: function () {
                fuzzyFormPanel.loadRecord(settingsRecord);
                fuzzyFormPanel.setLoading(false);
            }
        });
    },

    decodeRecords: function (decodedData) {
        decodedData = Ext.decode(decodedData);

        Ext.each(decodedData, function (data, index) {
            decodedData[index] = Ext.decode(data);
        });

        return decodedData;
    },

    mergeRecords: function(records, decodedRecords, compareField) {
        Ext.each(records, function (record) {
            Ext.each(decodedRecords, function (decodedRecord, index) {
                if (decodedRecord) {
                    if (record.data[compareField] == decodedRecord[compareField]) {
                        Ext.merge(record.data, decodedRecord);
                        record.save();
                        decodedRecords.splice(index, 1);
                    }
                }
            });
        });

        return decodedRecords;
    },

    showErrorMessage: function (record) {
        var message = Ext.String.format('{s name=controller/onApplyProfileRelevanceErrorText}The combination of field `[0]` and table [1] already exists for this shop!{/s}', record.get('field'), record.get('tableId'));
        Shopware.Notification.createGrowlMessage('{s name=controller/onApplyProfileRelevanceErrorTitle}Attention!{/s}', message);
    },

    onBeforeSaveProfile: function (controller, window, record) {
        var me = this,
            settingsRecord = me.getFuzzyFormPanel().getForm().getRecord(),
            settings = Ext.decode(Ext.encode(settingsRecord.data)),
            encodedSettings,
            relevanceRecords = me.getRelevanceGrid().getStore().data.items,
            encodedRelevance,
            searchTablesRecords = me.getSearchTablesGrid().getStore().data.items,
            encodedSearchTables;

        delete settings.id;
        delete settings.shopId;

        encodedSettings = Ext.encode(settings);
        encodedRelevance = me.encodeRecords(relevanceRecords);
        encodedSearchTables = me.encodeRecords(searchTablesRecords);

        record.set('settings', encodedSettings);
        record.set('relevance', encodedRelevance);
        record.set('searchTables', encodedSearchTables);
    },

    encodeRecords: function (recordsToEncode) {
        var encodedRecords = [],
            recordTmp;

        Ext.each(recordsToEncode, function (record) {
            recordTmp = Ext.decode(Ext.encode(record.data));
            delete recordTmp.id;
            encodedRecords.push(Ext.encode(recordTmp));
        });

        encodedRecords = Ext.encode(encodedRecords);

        return encodedRecords;
    },

    onBeforeAddSynonymGroup: function (controller, listing, record) {
        var me = this,
            shopId = me.getShopCombo().getValue();

        record.set('shopId', shopId);
    }
});
// {/block}
