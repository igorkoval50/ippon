// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/detail/relevance/relevance"}
Ext.define('Shopware.apps.SwagFuzzy.view.detail.relevance.Relevance', {
    extend: 'Shopware.model.Container',
    alias: 'widget.relevance-detail-container',
    padding: 10,

    configure: function () {
        var me = this,
            tablesStore = Ext.create('Shopware.apps.SwagFuzzy.store.SearchTables'),
            columnsStore = Ext.create('Shopware.apps.SwagFuzzy.store.TableColumns');

        tablesStore.load();

        return {
            controller: 'SwagFuzzyRelevance',
            splitFields: false,
            fieldSets: {
                title: '',
                fields: {
                    name: {
                        fieldLabel: '{s name=relevanceDetail/name}Relevance name{/s}',
                        allowBlank: false
                    },
                    relevance: {
                        fieldLabel: '{s name=relevanceDetail/relevance}Relevance value{/s}',
                        allowBlank: false
                    },
                    tableId: {
                        fieldLabel: '{s name=relevanceDetail/table}Choose table{/s}',
                        xtype: 'combobox',
                        store: tablesStore,
                        queryMode: 'local',
                        valueField: 'id',
                        displayField: 'table',
                        forceSelection: true,
                        allowBlank: false,
                        listeners: {
                            change: function (comboBox) {
                                columnsStore.getProxy().extraParams.tableName = comboBox.lastSelection[0].get('table');
                                columnsStore.load();
                                me.down('combobox[internalId=fieldComboBox]').setValue(me.record.get('field'));
                            }
                        }
                    },
                    field: {
                        xtype: 'combobox',
                        internalId: 'fieldComboBox',
                        fieldLabel: '{s name=relevanceDetail/field}Choose table field{/s}',
                        store: columnsStore,
                        queryMode: 'local',
                        valueField: 'columnName',
                        displayField: 'columnName',
                        forceSelection: true,
                        allowBlank: false
                    },
                    doNotSplit: {
                        fieldLabel: '{s name=search/detail/do_no_split_text namespace=backend/config/view/search}{/s}',
                        helpText: '{s name=search/detail/do_no_split_help_text namespace=backend/config/view/search}{/s}'
                    }
                }
            }
        };
    }
});
// {/block}
