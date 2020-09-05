
Ext.define('Shopware.apps.PremsEmotionCmsArticle2.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.premsemotioncms-list-article-window',
    height: 650,
    title : 'Einkaufswelten Artikel Übersicht',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.PremsEmotionCmsArticle2.view.list.Base',
            listingStore: 'Shopware.apps.PremsEmotionCmsArticle2.store.Base'
        };
    }
});