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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.optional.Field
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/optional"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.optional.Field', {
    'extend': 'Ext.form.FieldContainer',
    'alias': 'widget.neti_foundation-optional-field',
    'mixins': {
        'field': 'Ext.form.field.Field'
    },
    'requires': [],
    'layout': 'fit',

    'initComponent': function () {
        var me = this;

        me.items = [
            me.getCombobox(),
            me.getFormPanel()
        ];

        me.callParent(arguments);

        me.initField();
    },

    'getStore': function () {
        var me = this;

        if (!(me.store instanceof Ext.data.Store)) {
            me.store = me.createStore();
        }

        return me.store;
    },

    'createStore': function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            'fields': [
                {
                    'name': 'type',
                    'type': 'int'
                },
                {
                    'name': 'name',
                    'type': 'string'
                },
                {
                    'name': 'enable',
                    'useNull': true,
                    'type': 'boolean'
                }
            ],
            'data': me.store
        });
    },

    'getCombobox': function () {
        var me = this;

        return me.combobox || me.createCombobox();
    },

    'createCombobox': function () {
        var me = this;

        me.combobox = Ext.create('Ext.form.field.ComboBox', {
            'store': me.getStore(),
            'editable': false,
            'displayField': 'name',
            'valueField': 'type',
            'forceSelection': true,
            'value': me.value,
            'listeners': {
                'change': function (combobox, newValue) {
                    var store = me.getStore(),
                        record = store.findRecord('type', newValue, 0, false, false, true);

                    if (record) {
                        me.getFormPanel().setDisabled(!record.get('enable'));
                        //me.getFormPanel().getForm().getFields().each(function(field) {
                        //    field.setDisabled(!record.get('enable'));
                        //});
                    }
                }
            }
        });

        return me.combobox;
    },

    'getFormPanel': function () {
        var me = this;

        return me.formPanel || me.createFormPanel();
    },

    'createFormPanel': function () {
        var me = this;

        me.formPanel = Ext.create('Ext.form.Panel', {
            'border': null,
            'layout': 'fit',
            //'disabled': true,
            'items': me.items
        });

        me.getFormPanel().setDisabled(true);

        //me.formPanel.getForm().getFields().each(function(field) {
        //    field.setDisabled(true);
        //});

        return me.formPanel;
    },

    'reset': function () {
        var me = this;
        me.beforeReset();
        me.getCombobox().reset();
        me.clearInvalid();
        // delete here so we reset back to the original state
        delete me.wasValid;

        me.getFormPanel().setDisabled(true);

        //me.getFormPanel().getForm().getFields().each(function(field) {
        //    field.setDisabled(true);
        //});
    },

    'getValue': function () {
        var me = this;

        return me.getCombobox().getValue();
    },

    'setValue': function (values) {
        var me = this;

        return me.getCombobox().setValue(values);
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

        if (me.allowBlank || me.getFormPanel().isDisabled()) {
            return true;
        }

        return me.getValue();
    }
});
//{/block}
