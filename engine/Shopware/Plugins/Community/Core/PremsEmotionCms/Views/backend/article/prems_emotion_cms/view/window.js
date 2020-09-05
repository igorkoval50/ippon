//{namespace name=backend/prems_emotion_cms/article/view/window}
//{block name="backend/article/view/detail/window" append}
Ext.define('Shopware.apps.Article.PremsEmotionCms.view.Window', {

    override: 'Shopware.apps.Article.view.detail.Window',

    /**
     * @Override
     * Creates the main tab panel which displays the different tabs for the article sections.
     * To extend the tab panel this function can be override.
     *
     * @return Ext.tab.Panel
     */
    createMainTabPanel: function() {
        var me = this, result;

        result = me.callParent(arguments);

        me.registerAdditionalTab({
            title: '{s name=window/tab_prems_emotion_cms}{/s}',
            contentFn: me.createPremsEmotionCms,
            articleChangeFn: me.articleChange,
            tabConfig: {
                layout: {
                    type: 'hbox',
                    align: 'stretch'
                },
                listeners: {
                    activate: function () {
                        me.PremsEmotionCmsStore.load();
                        me.fireEvent('PremsEmotionCmsActivated', me);
                    }
                }
            },
            scope: me
        });

        return result;
    },

    /**
     * Callback function called when the article changed (splitview).
     *
     * @param article
     * @param tabConfig
     */
    articleChange: function(article, tabConfig) {
        var me = this;

        me.PremsEmotionCmsStore.getProxy().extraParams.articleId = article.get('id');
        me.PremsEmotionCmsStore.load();
    },

    /**
     * @return Ext.container.Container
     */
    createPremsEmotionCms: function(article, stores, eOpts) {
        var me = this, disabled = true, tab = eOpts.tab;

        me.PremsEmotionCmsStore = Ext.create('Shopware.apps.Article.PremsEmotionCms.store.Article');
        me.PremsEmotionCmsStore.getProxy().extraParams.articleId = null;

        if (article.get('id')) {
            me.PremsEmotionCmsStore.getProxy().extraParams.articleId = article.get('id');
            disabled = article.get('id') === null;
        }

        me.PremsEmotionCms = Ext.create('Ext.container.Container', {
            flex: 1,
            layout: 'fit',
            items: [
                {
                    xtype: 'prems-emotion-cms-article-grid',
                    store: me.PremsEmotionCmsStore,
                    articleId: me.article.get('id')
                }
            ]
        });

        tab.add(me.PremsEmotionCms);
        tab.setDisabled(disabled);

        return me.PremsEmotionCms;
    }
});
//{/block}