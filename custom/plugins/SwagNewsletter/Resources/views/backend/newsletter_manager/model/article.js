//{block name="backend/newsletter_manager/model/article"}
Ext.define('Shopware.apps.NewsletterManager.model.Article', {
    /**
     * Extends the standard Ext Model
     * @string
     */
    extend: 'Ext.data.Model',

    /**
     * The fields used for this model
     * @array
     */
    fields: [
        //{block name="backend/newsletter_manager/model/field/article"}{/block}
        'position', 'type', 'ordernumber', 'name'
    ]
});
//{/block}
