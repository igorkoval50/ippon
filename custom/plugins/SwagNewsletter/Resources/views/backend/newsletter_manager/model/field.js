//{block name="backend/newsletter_manager/model/field"}
Ext.define('Shopware.apps.NewsletterManager.model.Field', {
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
        //{block name="backend/newsletter_manager/model/field/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'componentId', type: 'int' },
        { name: 'name', type: 'string' },
        { name: 'xType', type: 'string' },
        { name: 'valueType', type: 'string' },
        { name: 'supportText', type: 'string' },
        { name: 'helpTitle', type: 'string' },
        { name: 'helpText', type: 'string' },
        { name: 'fieldLabel', type: 'string' },
        { name: 'allowBlank', type: 'int' },
        { name: 'defaultValue', type: 'string' },
        { name: 'store', type: 'string' },
        { name: 'displayField', type: 'string' },
        { name: 'valueField', type: 'string' }
    ]
});
//{/block}
