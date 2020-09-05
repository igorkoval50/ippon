Ext.define('Shopware.apps.PremsEmotionCmsArticle2.view.detail.Emotion', {
    extend: 'Shopware.grid.Association',
    alias: 'widget.premsemotioncms-article-emotion-window',
    height: 200,
    title: 'Einkaufswelt',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsArticle2',
            columns: {
                name: {}
            }
        };
    }
});