/**
 *
 */
//{namespace name="plugins/neti_foundation/backend/export"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.export.Window', {
    'extend': 'Enlight.app.SubWindow',
    'alias': 'widget.neti_foundation_extensions_export_window',
    'modal': true,
    'autoShow': true,
    'layout': {
        'type': 'hbox',
        'align': 'stretch'
    },
    'snippets': {
        'window_title': '{s name="window_title"}to export{/s}',
        'cancel_button_text': '{s name="cancel_button_text"}Cancel{/s}',
        'to_export_button_text': '{s name="to_export_button_text"}to export{/s}',
        'export_generated_growl_title': '{s name="export_generated_growl_title"}to export{/s}',
        'export_generated_growl_message': '{s name="export_generated_growl_message"}The Export is generated{/s}'
    },
    'containerConfig': null,
    'width': 350,
    'height': 200,
    'initComponent': function () {
        var me = this,
            desktop = Shopware.app.Application.viewport.getActiveDesktop();

        if (me.width >= desktop.getWidth()) {
            me.width = desktop.getWidth() * 0.8;
        }

        if (me.height >= desktop.getHeight()) {
            me.height = desktop.getHeight() * 0.8;
        }

        if (!Ext.isObject(me.containerConfig)) {
            me.containerConfig = {};
        }

        me.title = me.snippets.window_title;
        me.items = [me.getFormPanel()];
        me.dockedItems = me.createDockedItems();
        me.callParent(arguments);
    },

    'getStore': function () {
        var me = this;

        return me.store;
    },

    'getSelection': function () {
        var me = this;

        return me.selection;
    },

    'getFormPanel': function () {
        var me = this;

        return me.formPanel || me.createFormPanel();
    },

    'createFormPanel': function () {
        var me = this;

        me.formPanel = Ext.create('Ext.form.Panel', {
            'items': me.createTabItems(),
            'flex': 1,
            'defaults': {
                'cls': 'shopware-form'
            },
            'layout': {
                'type': 'hbox',
                'align': 'stretch'
            }
        });

        return me.formPanel;
    },

    'createTabItems': function () {
        var me = this,
            items = [];

        items.push(Ext.apply({
            'xtype': 'neti_foundation_extensions_export_container',
            'selection': me.getSelection()
        }, me.containerConfig));

        return items;
    },

    'createDockedItems': function () {
        var me = this;

        return [
            me.createToolbar()
        ];
    },

    'createToolbar': function () {
        var me = this;

        me.toolbar = Ext.create('Ext.toolbar.Toolbar', {
            'items': me.createToolbarItems(),
            'dock': 'bottom'
        });
        return me.toolbar;
    },

    'createToolbarItems': function () {
        var me = this, items = [];

        items.push({
            'xtype': 'tbfill'
        });

        items.push(me.createCancelButton());

        items.push(me.createExportButton());

        return items;
    },

    'createCancelButton': function () {
        var me = this;

        me.cancelButton = Ext.create('Ext.button.Button', {
            'cls': 'secondary',
            'name': 'cancel-button',
            'text': me.snippets.cancel_button_text,
            'handler': function () {
                me.onCancel();
            }
        });
        return me.cancelButton;
    },

    'createExportButton': function () {
        var me = this;

        me.exportButton = Ext.create('Ext.button.Button', {
            'cls': 'primary',
            'name': 'detail-save-button',
            'text': me.snippets.to_export_button_text,
            'handler': function () {
                me.onExport();
            }
        });
        return me.exportButton;
    },

    'onExport': function () {
        var me = this,
            store = me.getStore();

        me.createExportForm(
            store.getProxy().api.export,
            me.getExportValues()
        );

        Shopware.Notification.createGrowlMessage(
            me.snippets.export_generated_growl_title,
            me.snippets.export_generated_growl_message
        );

        me.destroy();
    },

    'getExportValues': function () {
        var me = this,
            values = me.getFormPanel().getValues(),
            store = me.getStore(),
            sorters = [],
            selection = [],
            filters = [];

        store.sorters.each(function (item) {
            if (item.hasOwnProperty('direction') && item.hasOwnProperty('property')) {
                sorters.push({
                    'direction': item.direction,
                    'property': item.property
                })
            }
        });

        store.filters.each(function (item) {
            if (item.hasOwnProperty('value') && item.hasOwnProperty('property')) {
                filters.push({
                    'value': item.value,
                    'property': item.property,
                    'operator': item.operator,
                    'expression': item.expression
                })
            }
        });

        Ext.each(me.getSelection(), function (item) {
            if (item.isModel) {
                selection.push(item.getData());
            } else {
                selection.push(item);
            }
        });

        return Ext.apply(
            {
                'limit': store.pageSize,
                'page': store.currentPage,
                'start': (store.currentPage - 1) * store.pageSize,
                'sort': Ext.encode(sorters),
                'selection': Ext.encode(selection),
                'filter': Ext.encode(filters)
            },
            store.getProxy().extraParams,
            values
        )
    },

    'createExportForm': function (url, values) {
        var me = this,
            form,
            iframeId = 'download-' + Ext.id(),
            iframe,
            iframeDom;

        form = Ext.create('Ext.form.Panel', {
            'standardSubmit': true,
            'url': url,
            'method': 'POST'
        });

        iframe = Ext.create('Ext.Component', {
            'renderTo': Ext.getBody(),
            'hidden': true,
            'autoEl': {
                'tag': 'iframe',
                'name': iframeId
            },
            'listeners': {
                'afterrender': function (iframe) {
                    iframeDom = iframe.getEl().dom;

                    form.submit({
                        'target': iframeId,
                        'params': me.makeParams(values)
                    });
                }
            }
        });
    },

    'makeParams': function (oldParams) {
        var me = this,
            newParams = {};

        Ext.Object.each(oldParams, function (key, value) {
            if (Array.isArray(value)) {
                Ext.Array.each(value, function (item, index) {
                    if (Ext.isObject(item)) {
                        Ext.Object.each(item, function (k, v) {
                            if (v) {
                                newParams[key + '[' + index + '][' + k + ']'] = v;
                            }
                        });
                    } else {
                        newParams[key + '[' + index + ']'] = item;
                    }
                });
            } else {
                newParams[key] = value;
            }
        });
        return newParams;
    },

    'onCancel': function () {
        this.destroy();
    }
});
//{/block}
