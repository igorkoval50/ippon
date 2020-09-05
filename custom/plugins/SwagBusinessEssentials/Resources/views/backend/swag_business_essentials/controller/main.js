// {block name="backend/swag_business_essentials/controller/configuration"}
Ext.define('Shopware.apps.SwagBusinessEssentials.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;
        me.mainWindow = me.getView('main.Window').create({ }).show();
    }
});
// {/block}
