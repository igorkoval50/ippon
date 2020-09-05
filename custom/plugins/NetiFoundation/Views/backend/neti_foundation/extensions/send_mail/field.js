/**
 * global: Ext, Shopware
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.Field', {
    'extend': 'Ext.form.FieldContainer',
    'alias': 'widget.neti_foundation_extensions_send_mail_field',
    'mixins': {
        'field': 'Ext.form.field.Field'
    },
    'border': null,
    'layout': 'fit',
    'height': 400,
    'formPanelAlias': 'widget.neti_foundation_extensions_send_mail_form',
    'templatePrefix': null,
    'formPanelConfig': null,
    'initComponent': function () {
        var me = this;

        if (!Ext.isObject(me.formPanelConfig)) {
            me.formPanelConfig = {};
        }

        me.items = [
            me.getFormPanel()
        ];

        me.callParent(arguments);

        if (me.hidden) {
            me.onFieldHide();
        }

        me.addListener(
            'hide',
            me.onFieldHide
        );

        me.addListener(
            'show',
            me.onFieldShow
        );

        me.initField();
    },

    'onFieldHide': function () {
        var me = this,
            form = me.getFormPanel().getForm(),
            fields = form.getFields();

        fields.each(function (field) {
            field.disable();
        });
    },

    'onFieldShow': function () {
        var me = this,
            form = me.getFormPanel().getForm(),
            mailTemplateIdField = form.findField('mailTemplateId'),
            mailTemplateIdValue,
            fields = form.getFields();

        if (mailTemplateIdField) {
            mailTemplateIdValue = mailTemplateIdField.getValue();
        }

        fields.each(function (field) {
            if(field.initialConfig.disabled) {
                if (mailTemplateIdValue) {
                    field.enable();
                }
            } else {
                field.enable();
            }
        });
    },

    'getFormPanel': function () {
        var me = this;

        return me.formPanel || me.createFormPanel();
    },

    'createFormPanel': function () {
        var me = this;

        me.formPanel = Ext.createByAlias(me.formPanelAlias, Ext.apply({
            'padding': 0,
            'bodyPadding': 0,
            'templatePrefix': me.templatePrefix
        }, me.formPanelConfig));

        return me.formPanel;
    },

    'setValue': function (value) {
        var me = this;

        me.getFormPanel().getForm().setValues(value);
    },

    'getValue': function () {
        var me = this;

        return me.getFormPanel().getValues();
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
    }
});
//{/block}
