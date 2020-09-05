/**
 * Shopware UI - Blog detail page - Sidebar
 * The assigned_articles component contains the configuration elements for the assgined blog articles relations.
 */
//{namespace name=backend/blog/view/blog}
//{block name="backend/blog/view/detail/sidebar/seo" append}
Ext.define('Mbdus.apps.Blog.view.blog.detail.sidebar.Seo', {
    /**
     * Define that the billing field set is an extension of the Ext.form.FieldSet
     * @string
     */
    override:'Shopware.apps.Blog.view.blog.detail.sidebar.Seo',

    /**
     * Creates the form items.
     * @return
     */
    createFormItems: function() {
        var me = this,
        elements = me.callParent(arguments);
        
        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
            helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, über die der Blogartikel aufrufbar sein soll.{/s}'
        });

        elements = Ext.Array.insert(elements, 1, [me.mbdusSeoUrl]);
        
        if(me.detailRecord.raw){
        	if(me.detailRecord.raw.attribute){
        		me.mbdusSeoUrl.setValue(me.detailRecord.raw.attribute.mbdusSeoUrl);
        	}
        }
        
        return elements;
    }
});
//{/block}
