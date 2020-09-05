Ext.define('Shopware.apps.PremsEmotionCmsSite.store.Base', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsSite'
        };
    },
    model: 'Shopware.apps.PremsEmotionCmsSite.model.Base'
});