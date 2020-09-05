/**
 * Copyright notice
 *
 * (c) 2009-2016 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.category.List
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/category"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.category.List', {
    'extend': 'Ext.grid.Panel',
    'alias': 'widget.neti_foundation-category-list',
    'cls': Ext.baseCSSPrefix + 'category-list',
    'requires': [],
    'mixins': {
        'field': 'Ext.form.field.Field'
    },
    'addOnlyLeaf': false,

    'snippets': {
        'title': '{s name=category-list-title}Assigned categories{/s}',
        'name': '{s name=category-list-name_column}Category name{/s}',
        'delete': '{s name=category-list-delete_tooltip}Remove entry{/s}',
        'toolbar': {
            'delete': '{s name=category-list-toolbar-delete_button}Remove all selected entries{/s}',
            'add': '{s name=category-list-toolbar-add_button}Add entry{/s}',
            'search': '{s name=search}Search...{/s}'
        }
    },
    'bbar': null,

    'initComponent': function () {
        var me = this;

        if (!me.store) {
            me.store = me.createStore();
        }

        me.tbar = me.createToolbar();
        me.columns = me.getColumns();
        me.selModel = me.getGridSelModel();
        me.addCls(Ext.baseCSSPrefix + 'free-standing-grid');

        me.callParent(arguments);
        me.initField();
    },

    'createStore': function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            'autoDestroy': true,
            'autoSync': true,
            'fields': [
                'id',
                'name'
            ],
            'proxy': {
                'type': 'memory'
            },
            'data': []
        });
    },

    'createToolbar': function () {
        var me = this;

        me.deleteButton = Ext.create('Ext.button.Button', {
            iconCls: 'sprite-minus-circle-frame',
            text: me.snippets.toolbar.delete,
            disabled: true,
            handler: function () {
                me.removeEntries();
            }
        });

        me.addButton = Ext.create('Ext.button.Button', {
            iconCls: 'sprite-plus-circle-frame',
            text: me.snippets.toolbar.add,
            handler: function () {
                me.openAddEntryWindow();
            }
        });

        return Ext.create('Ext.toolbar.Toolbar', {
            'dock': 'top',
            'items': [
                me.deleteButton,
                me.addButton
            ]
        });
    },

    'removeEntries': function (records) {
        var me = this;

        if(!records) {
            records = me.selModel.getSelection();
        }

        me.getStore().remove(records);
    },

    'openAddEntryWindow': function () {
        var me = this,
            win = Ext.create('Shopware.apps.NetiFoundation.view.form.category.Window', {
                'gridField': me,
                'addOnlyLeaf': me.addOnlyLeaf
            });

        return win.toFront();
    },

    'getGridSelModel': function () {
        var me = this;

        return Ext.create('Ext.selection.CheckboxModel', {
            'listeners': {
                // Unlocks the save button if the user has checked at least one checkbox
                'selectionchange': function (sm, selections) {
                    if (me.deleteButton === null) {
                        return;
                    }
                    me.deleteButton.setDisabled(selections.length === 0);
                }
            }
        });
    },

    'getColumns': function () {
        var me = this,
            columns = [];

        columns.push({
            header: me.snippets.name,
            dataIndex: 'name',
            flex: 1
        });

        columns.push({
            'xtype': 'actioncolumn',
            'width': 25,
            'items': [
                {
                    'iconCls': 'sprite-minus-circle-frame',
                    'action': 'delete',
                    'tooltip': me.snippets.delete,
                    'handler': function (view, rowIndex, colIndex, item, opts, record) {
                        var records = [record];

                        me.removeEntries(records);
                    }
                }
            ]
        });

        return columns;
    },

    'getValue': function () {
        var me = this,
            values = [];

        me.getStore().each(function (model) {
            values.push(model.getData());
        });

        return values;
    },

    'setValue': function (values) {
        var me = this,
            store = me.getStore(),
            addValues = [];

        store.addListener(
            'clear',
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
