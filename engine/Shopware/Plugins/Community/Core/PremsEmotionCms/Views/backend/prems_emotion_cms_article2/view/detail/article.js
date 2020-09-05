Ext.define('Shopware.apps.PremsEmotionCmsArticle2.view.detail.Article', {
    extend: 'Shopware.grid.Association',
    alias: 'widget.premsemotioncms-article-article-window',
    height: 200,
    title: 'Artikel',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsArticle2',
            columns: {
                name: {}
            }
        };
    }
});