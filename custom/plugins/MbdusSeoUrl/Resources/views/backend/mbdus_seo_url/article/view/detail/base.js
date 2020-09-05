//{block name="backend/article/view/detail/base" append}
Ext.define('Shopware.apps.Article.MbdusSeoUrl.view.Base', {
    override: 'Shopware.apps.Article.view.detail.Base',
    
    createLeftElements: function() {
        var me =this, articleId = null, additionalText = null;
        var elements = me.callParent(arguments);
        
        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
            labelWidth: 155,
            translatable: true,
            helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, über die der Artikel aufrufbar sein soll.{/s}'
        });
        elements.push(me.mbdusSeoUrl);
        return elements;
    },
    
    onStoresLoaded: function(article, stores) {
        var me = this;
        // Change the title
        me.article = article;
        me.changeTitle();
     
        if(me.article.raw.attribute){
        	me.mbdusSeoUrl.setValue(me.article.raw.attribute.mbdusSeoUrl);
        }
        if(me.article.raw.mainDetail){
        	me.mbdusSeoUrl.setValue(me.article.raw.mainDetail.attribute.mbdusSeoUrl);
        }
        // Bind the stores on the left side
        me.supplierCombo.bindStore(stores['suppliers']);

        // Bind the stores to the comboboxes on the right side
        me.taxComboBox.bindStore(stores['taxes']);
        me.templateComboBox.bindStore(stores['templates']);
        me.priceGroupComboBox.bindStore(stores['priceGroups']);

        me.numberField.validationRequestParam = article.getMainDetail().first().get('id');
        me.callParent(arguments);
    }
});
//{/block}