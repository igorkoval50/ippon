/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   NetiFoundation
 * @author     bmueller
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.components.view.field.Grid', {
    'extend': 'Ext.form.FieldContainer',
    'alias': 'widget.neti_foundation_grid_field',
    'mixins': {
        'field': 'Ext.form.field.Field'
    },
    'layout': 'fit',

    'initComponent': function () {
        var me = this;

        if (!me.store) {
            me.store = Ext.data.StoreManager.lookup('ext-empty-store');
        }

        me.items = [
            me.getGrid()
        ];

        me.callParent(arguments);

        me.initField();
    },

    'getGrid': function () {
        var me = this;

        return me.grid || me.createGrid();
    },

    'createGrid': function () {
        var me = this;

        me.grid = Ext.create('Ext.grid.Panel', me.getGridConfig());

        return me.grid;
    },

    'getGridConfig': function () {
        var me = this,
            config;

        if (!Ext.isObject(me.gridConfig)) {
            me.gridConfig = {};
        }

        config = Ext.apply({
            'border': null,
            'remoteSort': false,
            'remoteFilter': false
        }, me.gridConfig);

        config.store = me.store;

        return config
    },

    'getStore': function () {
        var me = this;

        return me.getGrid().getStore();
    },

    'getValue': function () {
        var me = this,
            values = [],
            store = me.getStore(),
            data = store.data;

        if (store.snapshot) {
            data = store.snapshot;
        }

        data.each(function (model) {
            values.push(model.getData());
        });

        return values;
    },

    'setValue': function (values) {
        var me = this,
            store = me.getStore(),
            addValues = [];

        if(store.data.items.length > 0) {
            store.addListener(
                'bulkremove',
                function () {
                    if (Ext.isArray(values)) {
                        Ext.each(values, function (item) {
                            if (item.isModel) {
                                addValues.push(item.getData());
                            } else {
                                addValues.push(item);
                            }
                        });

                        store.add(addValues);
                    }
                },
                store,
                {
                    'single': true
                }
            );

            store.removeAll();
        } else {
            if (Ext.isArray(values)) {
                Ext.each(values, function (item) {
                    if (item.isModel) {
                        addValues.push(item.getData());
                    } else {
                        addValues.push(item);
                    }
                });

                store.add(addValues);
            }
        }
    },

    'getSubmitValue': function () {
        var me = this;

        return me.getValue();
    },

    'getSubmitData': function () {
        var me = this,
            result = {};

        result[me.getName()] = me.getValue();

        return result;
    },

    'getModelData': function () {
        var me = this;

        return me.getValue();
    },

    'isValid': function () {
        var me = this;

        return me.allowBlank || me.getStore().getCount() > 0;
    }
});
//{/block}
