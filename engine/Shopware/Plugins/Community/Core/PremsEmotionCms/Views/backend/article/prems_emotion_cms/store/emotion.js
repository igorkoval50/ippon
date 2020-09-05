//{block name="backend/article/store/prems_emotion_cms/emotion"}
Ext.define('Shopware.apps.Article.PremsEmotionCms.store.Emotion', {

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

    model: 'Shopware.apps.Article.PremsEmotionCms.model.Emotion'
});
//{/block}