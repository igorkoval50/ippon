//{block name="backend/mag_newsletter_box/controller/main"}
Ext.define('Shopware.apps.MagNewsletterBox.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;
        me.mainWindow = me.getView('list.Window').create({ }).show();
    }
});
//{/block}