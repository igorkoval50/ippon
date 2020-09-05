
Ext.define('Shopware.apps.PremsEmotionCmsSupplier.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.premsemotioncms-list-window',
    height: 650,
    title : 'Einkaufswelten Hersteller Übersicht',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.PremsEmotionCmsSupplier.view.list.Base',
            listingStore: 'Shopware.apps.PremsEmotionCmsSupplier.store.Base'
        };
    }
});