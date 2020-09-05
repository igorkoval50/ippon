//{block name="backend/newsletter_manager/model/newsletter_element"}
Ext.define('Shopware.apps.NewsletterManager.model.NewsletterElement', {
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
        //{block name="backend/newsletter_manager/model/newsletter_element/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'isNew', type: 'boolean', defaultValue: false },
        { name: 'newsletterId', type: 'int' },
        { name: 'componentId', type: 'int' },
        {
            name: 'startRow',
            type: 'int',
            convert: function (value, record) {
                if (Ext.isNumeric(value)) {
                    return value;
                }
                return record.get('position');
            }
        },
        { name: 'startCol', type: 'int' },
        { name: 'endRow', type: 'int' },
        { name: 'endCol', type: 'int' },
        { name: 'data' }
    ],
    associations: [
        {
            type: 'hasMany',
            model: 'Shopware.apps.NewsletterManager.model.Component',
            name: 'getComponent',
            associationKey: 'component'
        }
    ]
});
//{/block}
