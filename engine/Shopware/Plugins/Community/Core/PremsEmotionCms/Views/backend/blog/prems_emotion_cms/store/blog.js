//{block name="backend/blog/store/prems_emotion_cms/blog"}
Ext.define('Shopware.apps.Blog.PremsEmotionCms.store.Blog', {

    extend: 'Ext.data.Store',
    configure: function() {
        return { controller: 'PremsEmotionCmsBlog' };
    },

    /**
     * Auto load the store after the component
     * is initialized
     * @boolean
     */
    autoLoad: false,

    model: 'Shopware.apps.Blog.PremsEmotionCms.model.Blog'
});
//{/block}