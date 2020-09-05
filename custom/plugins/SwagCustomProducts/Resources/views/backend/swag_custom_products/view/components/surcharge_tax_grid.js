// {namespace name="backend/swag_custom_products/option/fields"}
// {block name="backend/swag_custom_products/components/surchargeTaxGrid"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.SurchargeTaxGrid', {
    extend: 'Ext.form.FieldContainer',

    name: 'surchargeTaxGrid',

    fieldLabel: '{s name="grid/title"}Prices{/s}',

    labelWidth: 130,

    uri: {
        customerGroupUrl: '{url controller="SwagCustomProducts" action="getCustomerGroups"}',
        taxRateUrl: '{url controller="SwagCustomProducts" action="getTaxes"}',
        pluginDataUrl: '{url controller="SwagCustomProducts" action="getPluginData"}'
    },

    snippets: {
        gridTitle: '{s name="grid/title"}Prices{/s}',
        surcharge: '{s name="surcharge/title"}Surcharge{/s}',
        percentageSurcharge: '{s name="percentageSurcharge/title"}Percentage{/s}',
        customerGroup: '{s name="surcharge/tax/customerGroup"}Customer group{/s}',
        taxRate: '{s name="surcharge/tax/rate/title"}Tax rate{/s}',
        addButtonText: '{s name="price/grid/add/button/text"}Add price{/s}',
        defaultGroup: '{s name="default/customer/group/description"}Default{/s}',
        netPrice: '{s name="default/customer/group/netPrice"}(Net price){/s}'
    },

    /**
     * init method
     */
    initComponent: function () {
        var me = this;

        me.loadPluginData();

        me.items = [
            me.createGrid()
        ];

        me.callParent(arguments);
    },

    /**
     * Fire a ajax call to get the default currency.
     */
    loadPluginData: function () {
        var me = this;

        me.callAjax(
            me.uri.pluginDataUrl,
            {},
            me.afterLoadPluginData,
            me,
            me.onErrorLoadPluginData
        );
    },

    /**
     * After load successfully the currency data set the response data
     * to "me.currencyObject" an refresh the grid.
     *
     * @param { object } response
     * @param { * } scope
     */
    afterLoadPluginData: function (response, scope) {
        var me = scope || this;

        me.currencyObject = response.data.currencyData;
        me.defaultCustomerGroup = response.data.defaultCustomerGroup;
        me.pluginDataIsLoaded = true;

        me.refreshGrid();
    },

    /**
     * @throws Exception
     */
    onErrorLoadPluginData: function () {
        throw new Error('Can not load the currency data object.');
    },

    /**
     * @param { string } url
     * @param { * } parameter
     * @param { function } callback
     * @param { * } scope
     * @param { function } onError
     */
    callAjax: function (url, parameter, callback, scope, onError) {
        Ext.Ajax.request({
            url: url,
            params: parameter,
            success: function (response) {
                var responseData = Ext.JSON.decode(response.responseText);

                callback(responseData, scope);
            },
            failure: function () {
                onError(scope);
            }
        });
    },

    /**
     * @returns { Ext.grid.Panel | * }
     */
    createGrid: function () {
        var me = this;

        me.grid = Ext.create('Ext.grid.Panel', {
            store: me.record.getPrices(),
            columns: me.createGridColumns(),
            plugins: me.createGridPlugins(),
            dockedItems: me.createDockedItems(),
            height: 300
        });

        return me.grid;
    },

    /**
     * Check if a Price is configured
     */
    checkGridStoreForDefault: function () {
        var me = this;

        if (me.grid.getStore().getCount() > 0) {
            return;
        }

        me.createDefaultPrice();
    },

    /**
     * Create the default price and add them to the PriceStore
     */
    createDefaultPrice: function () {
        var me = this,
            defaultPrice;

        if (!me.pluginDataIsLoaded) {
            Ext.Function.defer(me.createDefaultPrice, 20, me);
            return;
        }

        defaultPrice = Ext.create('Shopware.apps.SwagCustomProducts.model.Price', {
            customerGroupName: me.defaultCustomerGroup.description,
            customerGroupId: me.defaultCustomerGroup.id,
            taxId: me.taxRateEditor.getStore().first().get('id')
        });

        me.grid.getStore().add(defaultPrice);
    },

    /**
     * @returns { Array }
     */
    createGridColumns: function () {
        var me = this,
            actionColumnItems = me.createActionColumnItems();

        return [
            {
                text: me.snippets.surcharge,
                dataIndex: 'surcharge',
                flex: 1,
                renderer: me.surchargeRenderer,
                editor: me.createSurchargeEditor(),
                hideable: false,
                sortable: false,
                menuDisabled: true
            }, {
                text: me.snippets.surcharge + ' %',
                dataIndex: 'percentage',
                flex: 1,
                renderer: me.percentageRenderer,
                editor: me.createPercentageEditor(),
                hideable: false,
                sortable: false,
                menuDisabled: true
            }, {
                text: me.snippets.percentageSurcharge,
                dataIndex: 'isPercentageSurcharge',
                flex: 1,
                renderer: me.booleanColumnRenderer,
                editor: me.createBooleanEditor(),
                hideable: false,
                sortable: false,
                menuDisabled: true
            }, {
                text: me.snippets.taxRate,
                dataIndex: 'taxId',
                flex: 1,
                renderer: Ext.bind(me.taxRateRenderer, me),
                editor: me.createTaxRateEditor(),
                hideable: false,
                sortable: false,
                menuDisabled: true
            }, {
                text: me.snippets.customerGroup,
                dataIndex: 'customerGroupName',
                flex: 2,
                hideable: false,
                sortable: false,
                menuDisabled: true
            }, {
                xtype: 'actioncolumn',
                width: actionColumnItems.length * 30,
                items: actionColumnItems,
                hideable: false,
                sortable: false,
                menuDisabled: true
            }
        ];
    },

    /**
     * @returns { object }
     */
    createActionColumnItems: function () {
        var me = this,
            deleteButton = {
                iconCls: 'sprite-minus-circle-frame',
                handler: Ext.bind(me.deletePriceColumnHandler, me),
                getClass: function (obj, metadata, record) {
                    if (!me.defaultCustomerGroup) {
                        return;
                    }

                    if (record.get('customerGroupId') == me.defaultCustomerGroup.id) {
                        return 'x-hidden';
                    }
                }
            };

        return [
            deleteButton
        ];
    },

    /**
     * @param { Ext.grid.Panel | * } grid
     * @param { * } row
     * @param { * } col
     * @param { Ext.Button } button
     * @param { string } type
     * @param { Ext.data.Model | * }record
     */
    deletePriceColumnHandler: function (grid, row, col, button, type, record) {
        var me = this;

        if (record.get('customerGroupId') == me.defaultCustomerGroup.id) {
            return;
        }

        grid.getStore().remove(record);
    },

    /**
     * @returns { Array }
     */
    createGridPlugins: function() {
        var me = this,
            plugins = [];

        me.gridEditor = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToEdit: 1,
            listeners: {
                beforeedit: Ext.bind(me.onBeforeEdit, me),
                canceledit: Ext.bind(me.cancelEditingRowEditor, me),
                edit: Ext.bind(me.onEditingRowEditor, me)
            }
        });

        plugins.push(me.gridEditor);

        return plugins;
    },

    /**
     * @param { Ext.grid.Panel } grid
     * @param { Ext.grid.plugin.RowEditing } editor
     */
    onBeforeEdit: function(grid, editor) {
        var me = this;

        me.setSurchargeEditors(editor.record.get('isPercentageSurcharge'));
    },

    /**
     * @param { object } data
     * @param { object } context
     */
    onEditingRowEditor: function(data, context) {
        var isPercentage = data.context.newValues.isPercentageSurcharge;
        context.record.set('isPercentageSurcharge', isPercentage);

        if (isPercentage) {
            context.record.set('surcharge', 0.00);
            return;
        }

        context.record.set('percentage', 0.0);
    },

    /**
     * On cancel editing reset the changes
     */
    cancelEditingRowEditor: function (editor, data) {
        var me = this;

        data.record.reject();

        if (!data.record.get('taxId')) {
            me.grid.getStore().remove(data.record);
        }

        me.checkGridStoreForDefault();
    },

    /**
     * @returns { *[] }
     */
    createDockedItems: function () {
        var me = this,
            topBar;

        topBar = Ext.create('Ext.toolbar.Toolbar', {
            layout: 'vbox',
            dock: 'top',
            ui: 'shopware-ui',
            items: [
                me.createCustomerGroupEditor()
            ]
        });

        return [topBar];
    },

    /**
     * @returns { Ext.form.field.ComboBox | * }
     */
    createCustomerGroupEditor: function () {
        var me = this;

        me.customerGroupEditor = Ext.create('Ext.form.field.ComboBox', {
            fieldLabel: me.snippets.customerGroup,
            width: '100%',
            margin: '0 3 7 0',
            editable: false,
            store: me.createCustomerGroupStore(),
            displayField: 'description',
            valueField: 'id',
            listeners: {
                change: Ext.bind(me.onChangeCustomerGroupCombo, me)
            }
        });

        return me.customerGroupEditor;
    },

    /**
     * @param { Ext.form.field.ComboBox } comboBox
     * @param { string } newValue
     */
    onChangeCustomerGroupCombo: function (comboBox, newValue) {
        var me = this,
            group = comboBox.findRecordByValue(newValue),
            groupName;

        if (me.isCustomerGroupInGridStore(newValue) || !group) {
            return;
        }

        groupName = group.get('description');

        var record = Ext.create('Shopware.apps.SwagCustomProducts.model.Price', {
            customerGroupId: newValue,
            customerGroupName: groupName,
            taxId: me.taxRateEditor.getStore().first().get('id')
        });

        me.grid.getStore().add(record);
        me.gridEditor.startEdit(record, 0);
    },

    /**
     * @param { number } customerGroupId
     * @returns { boolean }
     */
    isCustomerGroupInGridStore: function (customerGroupId) {
        var me = this,
            isInStore = false;

        me.grid.getStore().each(function (priceRow) {
            if (customerGroupId == priceRow.get('customerGroupId')) {
                isInStore = true;
            }
        });

        return isInStore;
    },

    /**
     * @returns { Ext.form.field.Number | * }
     */
    createSurchargeEditor: function () {
        var me = this;

        me.surchargeEditor = Ext.create('Ext.form.field.Number', {
            anchor: '100%',
            allowBlank: false,
            decimalPrecision: 5,
            minValue: 0
        });

        return me.surchargeEditor;
    },

    /**
     * @return { Ext.form.field.Number }
     */
    createPercentageEditor: function() {
        var me = this;

        me.percentageEditor = Ext.create('Ext.form.field.Number', {
            anchor: '100%',
            allowBlank: false,
            decimalPrecision: 2,
            minValue: 0
        });

        return me.percentageEditor;
    },

    /**
     * @returns { Ext.form.field.ComboBox | * }
     */
    createTaxRateEditor: function () {
        var me = this;

        me.taxRateEditor = Ext.create('Ext.form.field.ComboBox', {
            store: me.createTaxRateStore(),
            displayField: 'name',
            allowBlank: false,
            valueField: 'id',
            forceSelection: true
        });

        return me.taxRateEditor;
    },

    /**
     * @return { Ext.form.field.Checkbox }
     */
    createBooleanEditor: function() {
        var me = this;

        return Ext.create('Ext.form.field.Checkbox', {
            listeners: {
                change: Ext.bind(me.onBooleanEditorChange, me)
            }
        });
    },

    /**
     * @param { Ext.form.field.Checkbox } checkbox
     * @param { boolean } newValue
     */
    onBooleanEditorChange: function(checkbox, newValue) {
        this.setSurchargeEditors(newValue);
    },

    /**
     * @param { boolean } checked
     */
    setSurchargeEditors: function(checked) {
        var me = this;

        if (checked) {
            me.surchargeEditor.disable();
            me.percentageEditor.enable();
            return;
        }

        me.surchargeEditor.enable();
        me.percentageEditor.disable();
    },

    /**
     * @returns { Ext.data.Store }
     */
    createTaxRateStore: function () {
        var me = this;

        return Ext.create('Shopware.apps.Base.store.Tax', {
            listeners: {
                load: Ext.bind(me.afterLoadTaxRateGrid, me)
            }
        }).load();
    },

    /**
     * @returns { Ext.data.Store }
     */
    createCustomerGroupStore: function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            fields: ['id', 'description', 'tax', 'taxinput'],
            proxy: {
                type: 'ajax',
                api: {
                    read: me.uri.customerGroupUrl
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                load: Ext.bind(me.afterLoadCustomerGroupStore, me)
            }
        }).load();
    },

    /**
     * @param { Ext.data.Store } store
     */
    afterLoadCustomerGroupStore: function (store) {
        var me = this;

        store.each(function (group) {
            if (group.get('taxinput') == '0') {
                var description = [
                    group.get('description'),
                    ' ',
                    me.snippets.netPrice
                ].join('');

                group.set('description', description);
            }
        });
    },

    /**
     * @param { float } value
     * @param { object } metaData
     * @param { Ext.data.Model } record
     * @returns { string }
     */
    surchargeRenderer: function(value, metaData, record) {
        if (record.get('isPercentageSurcharge')) {
            metaData.style = 'opacity: .5;';
        }

        return Ext.util.Format.currency(value, '€', 2, true);
    },

    /**
     * @param { float } value
     * @param { object } metaData
     * @param { Ext.data.Model } record
     * @returns { string }
     */
    percentageRenderer: function(value, metaData, record) {
        if (!record.get('isPercentageSurcharge')) {
            metaData.style = 'opacity: .5;';
        }

        return Ext.util.Format.currency(value, '%', 1, true);
    },

    /**
     * @param { boolean } value
     * @returns { string }
     */
    booleanColumnRenderer: function (value) {
        var checked = 'sprite-ui-check-box-uncheck';
        if (value === true) {
            checked = 'sprite-ui-check-box';
        }

        return '<span style="display:block; margin: 0 auto; height:16px; width:16px;" class="' + checked + '"></span>';
    },

    /**
     * @param { int } value
     * @returns { string }
     */
    taxRateRenderer: function (value) {
        var me = this,
            taxName = '';

        me.taxRateEditor.getStore().each(function (tax) {
            if (tax.get('id') == value) {
                taxName = tax.get('name');
            }
        });

        return taxName;
    },

    /**
     * After load check for prices. If no prices set, create a default price.
     * Disable the defaultPrice deleteButton
     */
    afterLoadTaxRateGrid: function () {
        var me = this;

        me.checkGridStoreForDefault();

        me.refreshGrid();
    },

    /**
     * Refresh the grid view
     */
    refreshGrid: function () {
        var me = this;

        me.grid.getView().refresh();
    },

    /**
     * reset the store to a state
     */
    reset: function () {
        var me = this;

        me.grid.getStore().rejectChanges();
    }
});
// {/block}
