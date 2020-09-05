Ext.define('Shopware.apps.Promotionbanner.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.promotionbanner-list-window',
    height: 500,
    width: 1000,
    title : '{s name=PromotionbannerWindowTitle}Promotionbanner{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.Promotionbanner.view.list.Promotionbanner',
            listingStore: 'Shopware.apps.Promotionbanner.store.Promotionbanner'
        };
    }
});