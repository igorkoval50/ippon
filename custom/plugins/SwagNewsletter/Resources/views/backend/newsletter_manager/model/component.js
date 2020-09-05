//{block name="backend/newsletter_manager/model/component"}
Ext.define('Shopware.apps.NewsletterManager.model.Component', {
    /**
     * Extends the standard Ext Model
     * @string
     */
    extend: 'Ext.data.Model',

    requires: [
        'Shopware.apps.NewsletterManager.model.Field'
    ],

    /**
     * The fields used for this model
     * @array
     */
    fields: [
        //{block name="backend/newsletter_manager/model/component/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'pluginId', type: 'int', useNull: true },
        { name: 'name', type: 'string' },
        { name: 'description', type: 'string' },
        { name: 'xType', type: 'string' },
        { name: 'template', type: 'string' },
        { name: 'cls', type: 'string' }
    ],
    associations: [
        {
            type: 'hasMany',
            model: 'Shopware.apps.NewsletterManager.model.Field',
            name: 'getComponentFields',
            associationKey: 'componentFields'
        }
    ]
});
//{/block}
