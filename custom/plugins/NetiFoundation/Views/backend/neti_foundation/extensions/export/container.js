/**
 *
 */
//{namespace name="plugins/neti_foundation/backend/export"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.export.Container', {
    'extend': 'Ext.container.Container',
    'alias': 'widget.neti_foundation_extensions_export_container',
    'padding': 20,

    'snippets': {
        'field_label_export_as': '{s name="field_label_export_as"}Export as{/s}',
        'field_label_to_export': '{s name="field_label_to_export"}To export{/s}',
        'field_label_export_as_xls': '{s name="field_label_export_as_xls"}XLS{/s}',
        'field_label_export_as_csv': '{s name="field_label_export_as_csv"}CSV{/s}',
        'field_label_export_current_page': '{s name="field_label_export_current_page"}Current page{/s}',
        'field_label_export_selection': '{s name="field_label_export_selection"}Selection{/s}',
        'field_label_export_all': '{s name="field_label_export_all"}All{/s}'
    },

    'initComponent': function () {
        var me = this;

        me.items = me.createItems();

        me.callParent(arguments);
    },

    'createFieldSetItems': function () {
        var me = this,
            items = [];

        items.push({
            'xtype': 'combobox',
            'name': 'exportAs',
            'forceSelection': true,
            'allowBlank': false,
            'editable': false,
            'fieldLabel': me.snippets.field_label_export_as,
            'displayField': 'text',
            'valueField': 'value',
            'store': me.getExportAsStore(),
            'listeners': {
                'afterrender': function () {
                    if (!this.getValue()) {
                        this.select(this.getStore().getAt(0));
                        this.doQuery();
                    }
                }
            }
        });

        items.push({
            'xtype': 'combobox',
            'name': 'toExport',
            'forceSelection': true,
            'allowBlank': false,
            'editable': false,
            'fieldLabel': me.snippets.field_label_to_export,
            'displayField': 'text',
            'valueField': 'value',
            'store': me.getToExportStore(),
            'listeners': {
                'afterrender': function () {
                    if (!this.getValue()) {
                        this.select(this.getStore().getAt(0));
                        this.doQuery();
                    }
                }
            }
        });

        return items;
    },

    'getExportAsStore': function () {
        var me = this;

        return me.exportAsStore || me.createExportAsStore()
    },

    'createExportAsStore': function () {
        var me = this;

        me.exportAsStore = Ext.create('Ext.data.Store', {
            'fields': ['value', 'text'],
            'data': [
                {
                    'text': me.snippets.field_label_export_as_xls,
                    'value': 'xls'
                },
                {
                    'text': me.snippets.field_label_export_as_csv,
                    'value': 'csv'
                }
            ]
        });

        return me.exportAsStore;
    },

    'getToExportStore': function () {
        var me = this;

        return me.toExportStore || me.createToExportStore()
    },

    'createToExportStore': function () {
        var me = this,
            items = [];

        items.push({
            'text': me.snippets.field_label_export_current_page,
            'value': 'currentPage'
        });

        if (me.selection.length > 0) {
            items.push({
                'text': me.snippets.field_label_export_selection,
                'value': 'selection'
            });
        }

        items.push({
            'text': me.snippets.field_label_export_all,
            'value': 'all'
        });

        me.toExportStore = Ext.create('Ext.data.Store', {
            'fields': ['value', 'text'],
            'data': items
        });

        return me.toExportStore;
    },

    'createItems': function () {
        var me = this;

        return Ext.create('Ext.form.FieldSet', {
            'flex': 1,
            'padding': '10 20',
            'layout': {
                'type': 'vbox',
                'align': 'stretch'
            },
            'items': me.createFieldSetItems()
        });
    }
});
//{/block}
