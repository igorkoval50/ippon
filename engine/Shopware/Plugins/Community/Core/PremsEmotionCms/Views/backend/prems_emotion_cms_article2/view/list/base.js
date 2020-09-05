Ext.define('Shopware.apps.PremsEmotionCmsArticle2.view.list.Base', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.premsemotioncms-article-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.PremsEmotionCmsArticle2.view.detail.Window',
            columns: {
                name: {
                    header: 'Name',
                    flex: 3
                }
            }
        };
    }
});
