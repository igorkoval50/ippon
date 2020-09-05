//{block name="backend/newsletter_manager/view/components/fields/article"}
Ext.define('Shopware.apps.NewsletterManager.view.components.fields.Article', {
    extend: 'Shopware.form.field.ArticleSearch',
    alias: 'widget.newsletter-components-fields-article',
    hiddenReturnValue: 'number',
    returnValue: 'number',

    initComponent: function () {
        var me = this;
        me.hiddenFieldName = me.name;

        me.callParent(arguments);
    },

    createHiddenField: function () {
        var me = this,
            input = Ext.create('Ext.form.field.Hidden', {
                name: me.hiddenFieldName,
                valueType: me.valueType,
                fieldId: me.fieldId
            });
        return input;
    }
});
//{/block}
