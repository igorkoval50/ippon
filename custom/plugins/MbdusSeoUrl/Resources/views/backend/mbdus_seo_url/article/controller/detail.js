//{block name="backend/article/controller/detail" append}
Ext.define('Mbdus.apps.Article.controller.Detail', {
    override: 'Shopware.apps.Article.controller.Detail',

    onSaveArticle: function(win, article, options) {
        var me = this,
            originalCallback = options.callback;
       
        var customCallback = function(newArticle, success) {
            Ext.Ajax.request({
                method: 'POST',
                url: '{url controller=AttributeData action=saveData}',
                params: {
                    _foreignKey: newArticle.get('mainDetailId'),
                    _table: 's_articles_attributes',
                    __attribute_mbdus_seourl: me.getBaseFieldSet().mbdusSeoUrl.getValue()
                }
            });
            Ext.callback(originalCallback, this, arguments);
        };

        if (!options.callback || options.callback.toString() !== customCallback.toString()) {
        	customCallback(article, options);
        }

        me.callParent([win, article, options]);
    }
});
//{/block}