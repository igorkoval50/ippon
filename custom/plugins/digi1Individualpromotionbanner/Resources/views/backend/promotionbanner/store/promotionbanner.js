Ext.define('Shopware.apps.Promotionbanner.store.Promotionbanner', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return { controller: 'Promotionbanner' };
    },
    model: 'Shopware.apps.Promotionbanner.model.Promotionbanner',
});