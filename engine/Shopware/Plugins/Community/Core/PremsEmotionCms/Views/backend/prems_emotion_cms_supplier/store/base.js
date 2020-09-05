Ext.define('Shopware.apps.PremsEmotionCmsSupplier.store.Base', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsSupplier'
        };
    },
    model: 'Shopware.apps.PremsEmotionCmsSupplier.model.Base'
});