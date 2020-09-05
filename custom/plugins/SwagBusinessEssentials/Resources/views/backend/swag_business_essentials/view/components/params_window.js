// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/components/params_window"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.components.ParamsWindow', {
    extend: 'Enlight.app.Window',
    alias: 'widget.business_essentials-params_window',
    title: '{s name="PrivateShoppingParamsWindowTitle"}Parameter details{/s}',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },

    width: 350,
    height: 450,

    /* Filled externally */
    data: null,

    filterRegex: /[A-Za-z0-9._öäüÖÄÜ-]/,

    /**
     * Creates the main-form of the window and the docked-items, which is only a save- and a cancel-button.
     */
    initComponent: function() {
        var me = this;

        me.items = [ me.getForm() ];
        me.dockedItems = me.createDockedItems();

        me.callParent(arguments);
    },

    /**
     * Creates the main form containing the text-fields to set a new param and the grid.
     * Additionally, a small information container is created.
     *
     * @returns { Ext.form.Panel }
     */
    getForm: function() {
        var me = this;

        return Ext.create('Ext.form.Panel', {
            flex: 1,
            items: [
                me.getConfigFieldSet(),
                me.getGrid(),
                me.createInfoText()
            ]
        });
    },

    /**
     * @returns { Ext.form.FieldSet }
     */
    getConfigFieldSet: function() {
        var me = this;

        return Ext.create('Ext.form.FieldSet', {
            layout: 'column',
            border: 0,
            margin: 0,
            title: '',
            padding: 10,
            items: me.getConfigFields(),
            defaults: {
                margin: '0 3 7 0'
            }
        });
    },

    /**
     * Creates the fields to set a new parameter.
     *
     * @returns { Array }
     */
    getConfigFields: function() {
        var me = this,
            listener = {
                change: function() {
                    me.addButton.setDisabled(!me.necessaryFieldsValid());
                },
                keypress: Ext.bind(me.onKeyPress, me)
            };

        me.keyField = Ext.create('Ext.form.field.Text', {
            fieldLabel: '{s name="PrivateShoppingParamsColumnName"}Name{/s}',
            columnWidth: 1,
            listeners: listener,
            enableKeyEvents: true
        });

        me.valueField = Ext.create('Ext.form.field.Text', {
            fieldLabel: '{s name="PrivateShoppingParamsColumnValue"}Value{/s}',
            columnWidth: 1,
            listeners: listener,
            enableKeyEvents: true
        });

        me.addButton = Ext.create('Ext.button.Button', {
            text: '{s name="PrivateShoppingParamsAddBtn"}Add{/s}',
            cls: 'primary small',
            columnWidth: 0.3,
            disabled: true,
            margin: 0,
            style: {
                float: 'right'
            },
            handler: function() {
                me.gridStore.add({
                    key: me.keyField.getValue(),
                    value: me.valueField.getValue()
                });

                me.keyField.reset();
                me.valueField.reset();
            }
        });

        return [
            me.keyField,
            me.valueField,
            me.addButton
        ];
    },

    /**
     * @returns { Ext.grid.Panel}
     */
    getGrid: function() {
        var me = this,
            editor = {
                xtype: 'textfield',
                allowBlank: false
            };

        me.grid = Ext.create('Ext.grid.Panel', {
            title: '{s name="PrivateShoppingParamsTitle"}Parameters{/s}',
            plugins: me.getEditingPlugin(),
            border: 0,
            height: 210,
            columns: [
                {
                    text: '{s name="PrivateShoppingParamsColumnName"}Name{/s}',
                    dataIndex: 'key',
                    sortable: false,
                    hideable: false,
                    flex: 9,
                    editor: editor
                }, {
                    text: '{s name="PrivateShoppingParamsColumnValue"}Value{/s}',
                    dataIndex: 'value',
                    sortable: false,
                    hideable: false,
                    flex: 8,
                    editor: editor
                }, {
                    xtype: 'actioncolumn',
                    text: '{s name="PrivateShoppingParamsActionColumn"}{/s}',
                    sortable: false,
                    hideable: false,
                    flex: 4,
                    items: me.createActionColumnItems()
                }
            ],
            store: me.createGridStore()
        });

        return me.grid;
    },

    /**
     * @returns { [ Ext.toolbar.Toolbar ] }
     */
    createDockedItems: function () {
        var me = this;

        return [
            me.createToolbar()
        ];
    },

    /**
     * @returns { Ext.toolbar.Toolbar }
     */
    createToolbar: function() {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            items: me.createToolbarItems(),
            dock: 'bottom'
        });
    },

    /**
     * @returns { Ext.button.Button[] }
     */
    createToolbarItems: function() {
        var me = this, items = [];

        items.push({ xtype: 'tbfill' });
        items.push(me.createCancelButton());
        items.push(me.createSaveButton());

        return items;
    },

    /**
     * @returns { Ext.button.Button }
     */
    createCancelButton: function () {
        var me = this;

        return Ext.create('Ext.button.Button', {
            cls: 'secondary',
            name: 'cancel-button',
            text: '{s namespace="backend/application/main" name="detail_window/cancel_button_text"}Cancel{/s}',
            handler: function () {
                me.destroy();
            }
        });
    },

    /**
     * @returns { Ext.button.Button }
     */
    createSaveButton: function () {
        var me = this;

        return Ext.create('Ext.button.Button', {
            cls: 'primary',
            name: 'detail-save-button',
            text: '{s namespace="backend/application/main" name="detail_window/save_button_text"}Save{/s}',
            handler: function () {
                var values = [];

                me.gridStore.each(function (item) {
                    values.push({ key: item.get('key'), value: item.get('value') });
                });

                me.field.setValue(values);
                me.field.onChange();
                me.destroy();
            }
        });
    },

    /**
     * @returns { Ext.data.Store }
     */
    createGridStore: function() {
        var me = this,
            data = me.data || [];

        me.gridStore = Ext.create('Ext.data.Store', {
            model: 'Shopware.apps.SwagBusinessEssentials.model.Params',
            data: data
        });

        return me.gridStore;
    },

    /**
     * @returns { Ext.container.Container }
     */
    createInfoText: function() {
        return Ext.create('Ext.container.Container', {
            style: {
                color: '#61677f',
                fontStyle: 'italic'
            },
            padding: 10,
            html: '{s name="PrivateShoppingParamsInfo"}You can define further parameters for the redirection in this window.{/s}'
        });
    },

    /**
     * @returns { Array }
     */
    createActionColumnItems: function() {
        return [
            this.createEditButton(),
            this.createDeleteButton()
        ];
    },

    /**
     * @returns { Object }
     */
    createEditButton: function() {
        var me = this;

        return {
            action: 'editParam',
            iconCls: 'sprite-pencil',
            handler: function(view, rowIndex, colIndex, item, event, record) {
                me.grid.getPlugin('params-row-editing').startEdit(record, 0);
            }
        };
    },

    /**
     * @returns { Object }
     */
    createDeleteButton: function() {
        var me = this;

        return {
            action: 'deleteParam',
            iconCls: 'sprite-minus-circle-frame',
            handler: function(view, rowIndex) {
                me.gridStore.removeAt(rowIndex);
            }
        };
    },

    /**
     * Checks if all necessary fields are properly filled in order to enable the "add"-button.
     *
     * @returns { boolean }
     */
    necessaryFieldsValid: function() {
        var me = this;

        if (!me.isKeyValueDuplicated(me.keyField.getValue()) &&
            me.isFieldValid(me.keyField) &&
            me.isFieldValid(me.valueField)
        ) {
            return true;
        }

        return false;
    },

    /**
     * Checks if the given field is valid due to its value.
     *
     * @param { Ext.form.field.Text } field
     * @returns { boolean }
     */
    isFieldValid: function(field) {
        if (!field) {
            return false;
        }

        return !!field.getValue();
    },

    /**
     * Checks if the given key-value is duplicated.
     *
     * @param { string } value
     * @returns { boolean }
     */
    isKeyValueDuplicated: function(value) {
        var me = this,
            isDuplicated = false;

        me.gridStore.each(function(item) {
            if (item.get('key') === value) {
                isDuplicated = true;
                return false;
            }
        });

        return isDuplicated;
    },

    /**
     * Validates the key pressed and prevents it if necessary.
     *
     * @param { Ext.form.field.Text } textField
     * @param { Ext.EventObject } event
     */
    onKeyPress: function(textField, event) {
        var me = this,
            char = String.fromCharCode(event.charCode || event.keyCode),
            regex = new RegExp(me.filterRegex);

        if (regex.test(char) || me.isValidCode(event)) {
            return;
        }

        event.preventDefault();
    },

    /**
     * Checks if the key-code equals the keys for "TAB" and "BACKSPACE".
     *
     * @param { Ext.EventObject } event
     * @returns { boolean }
     */
    isValidCode: function(event) {
        var code = event.keyCode;

        return !!(code === event.TAB || code === event.BACKSPACE);
    },

    /**
     * @returns { Ext.grid.plugin.Editing[] }
     */
    getEditingPlugin: function() {
        return [
            Ext.create('Ext.grid.plugin.RowEditing', {
                clicksToEdit: 2,
                pluginId: 'params-row-editing'
            })
        ];
    }
});
// {/block}
