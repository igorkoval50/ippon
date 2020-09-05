//{namespace name=backend/prems_emotion_cms/blog/view/window}
//{block name="backend/blog/view/blog/window" append}
Ext.define('Shopware.apps.Blog.PremsEmotionCms.view.Window', {

    override: 'Shopware.apps.Blog.view.blog.Window',

    getTabs:function () {
        var me = this, result;

        result = me.callParent(arguments);

        me.PremsEmotionCmsStore = Ext.create('Shopware.apps.Blog.PremsEmotionCms.store.Blog');
        me.PremsEmotionCmsStore.getProxy().extraParams.blogId = me.record.get('id');
        me.PremsEmotionCmsStore.load();

        result.push({
            xtype: 'prems-emotion-cms-blog-grid',
            title: '{s name=window/tab_prems_emotion_cms}{/s}',
            store: me.PremsEmotionCmsStore,
            blogId: me.record.get('id')
        });

        return result;
    },


    /**
     * Callback function called when the blog changed.
     *
     * @param blog
     * @param tabConfig
     */
    /**blogChange: function(blog, tabConfig) {
        var me = this;

        me.PremsEmotionCmsStore.getProxy().extraParams.blogId = blog.get('id');
        me.PremsEmotionCmsStore.load();
    },*/

});
//{/block}