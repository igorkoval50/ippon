//{block name="backend/article/store/prems_emotion_cms/article"}
Ext.define('Shopware.apps.Article.PremsEmotionCms.store.Article', {

    extend: 'Ext.data.Store',
    configure: function() {
        return { controller: 'PremsEmotionCmsArticle' };
    },

    /**
     * Auto load the store after the component
     * is initialized
     * @boolean
     */
    autoLoad: false,

    model: 'Shopware.apps.Article.PremsEmotionCms.model.Article'
});
//{/block}