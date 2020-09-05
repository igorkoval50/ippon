//

// {block name="backend/swag_custom_products/model/value"}
Ext.define('Shopware.apps.SwagCustomProducts.model.Value', {
    extend: 'Ext.data.Model',

    fields: [
        // {block name="backend/swag_custom_products/model/value/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'name', type: 'string' },
        { name: 'value', type: 'string', useNull: true },
        { name: 'ordernumber', type: 'string', useNull: true },
        { name: 'position', type: 'int', useNull: true },
        { name: 'isDefaultValue', type: 'boolean', defaultValue: false, useNull: true },
        { name: 'optionId', type: 'int', useNull: true },
        { name: 'surcharge', type: 'float', defaultValue: 0.00, useNull: true },
        { name: 'isOnceSurcharge', type: 'boolean', defaultValue: false, useNull: true },
        { name: 'surchargeTaxRate', type: 'int', useNull: true },
        { name: 'mediaId', type: 'int', defaultValue: null, useNull: true },
        { name: 'seoTitle', type: 'string', defaultValue: null, useNull: true }
    ],

    associations: [
        {
            type: 'hasMany',
            model: 'Shopware.apps.SwagCustomProducts.model.Price',
            associationKey: 'prices',
            name: 'getPrices'
        }
    ]
});
// {/block}
