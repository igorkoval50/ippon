
Ext.define('Shopware.apps.StuttSeoRedirects.store.Redirect', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'StuttSeoRedirects'
        };
    },
    model: 'Shopware.apps.StuttSeoRedirects.model.Redirect'
});