//{block name="backend/newsletter_manager/model/link"}
Ext.define('Shopware.apps.NewsletterManager.model.Link', {
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
        //{block name="backend/newsletter_manager/model/field/link"}{/block}
        'position', 'link', 'description', 'target'
    ]
});
//{/block}
