
Ext.define('Shopware.apps.PremsEmotionCmsSite.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.premsemotioncms-list-window',
    height: 650,
    title : 'Einkaufswelten Shopseiten Übersicht',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.PremsEmotionCmsSite.view.list.Base',
            listingStore: 'Shopware.apps.PremsEmotionCmsSite.store.Base'
        };
    }
});