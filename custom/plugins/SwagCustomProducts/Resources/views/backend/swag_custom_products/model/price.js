//

// {block name="backend/swag_custom_products/model/price"}
Ext.define('Shopware.apps.SwagCustomProducts.model.Price', {
    extend: 'Ext.data.Model',

    fields: [
        // {block name="backend/swag_custom_products/model/price/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'surcharge', type: 'float', defaultValue: 0.00, useNull: false, convert: null },
        { name: 'percentage', type: 'float', defaultValue: 0.0 },
        { name: 'isPercentageSurcharge', type: 'boolean' },
        { name: 'customerGroupId', type: 'int', useNull: false },
        { name: 'customerGroupName', type: 'string', useNull: false },
        { name: 'taxId', type: 'int', useNull: true, defaultValue: null }
    ]
});
// {/block}
