/**
 * global: Ext, Shopware
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.Window', {
    'extend': 'Enlight.app.Window',
    'alias': 'widget.neti_foundation_extensions_send_mail_window',
    'requires': [],
    'onSuccess': Ext.emptyFn,
    'onFailure': Ext.emptyFn,
    'createFormButtons': Ext.emptyFn,
    'formPanelAlias': 'widget.neti_foundation_extensions_send_mail_form',
    'layout': 'border',
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

        me.dockedItems = [{
            dock: 'bottom',
            xtype: 'toolbar',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items: me.createFormButtons()
        }];
        me.callParent(arguments);
    },

    'getFormPanel': function () {
        var me = this;

        return me.formPanel || me.createFormPanel();
    },

    'createFormPanel': function () {
        var me = this;

        me.formPanel = Ext.createByAlias(me.formPanelAlias, Ext.apply({
            'templatePrefix': me.templatePrefix,
            'url': me.url
        }, me.formPanelConfig));

        return me.formPanel;
    },

    'getRemoteStore': function () {
        var me = this;

        return me.getFormPanel().getRemoteStore();
    },

    'getEditorTabPanel': function () {
        var me = this;

        return me.getFormPanel().getEditorTabPanel();
    },

    'getTemplateModel': function () {
        var me = this;

        return me.getFormPanel().getTemplateModel();
    }
});
//{/block}
