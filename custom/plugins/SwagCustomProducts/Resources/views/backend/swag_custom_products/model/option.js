//

// {block name="backend/swag_custom_products/model/option"}
Ext.define('Shopware.apps.SwagCustomProducts.model.Option', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            detail: 'Shopware.apps.SwagCustomProducts.view.option.Detail',
            listing: 'Shopware.apps.SwagCustomProducts.view.detail.OptionList'
        };
    },

    fields: [
        // {block name="backend/swag_custom_products/model/option/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'name', type: 'string', useNull: true },
        { name: 'description', type: 'string', useNull: true },
        { name: 'ordernumber', type: 'string', useNull: true },
        { name: 'required', type: 'boolean', defaultValue: false, useNull: true },
        { name: 'type', type: 'string', useNull: false },
        { name: 'couldContainValues', type: 'boolean', useNull: false },
        { name: 'position', type: 'int', useNull: false },
        { name: 'defaultValue', type: 'string', useNull: true },
        { name: 'placeholder', type: 'string', useNull: true },
        { name: 'isOnceSurcharge', type: 'boolean', defaultValue: false, useNull: true },
        { name: 'templateId', type: 'int', useNull: true },
        { name: 'maxTextLength', type: 'int', useNull: true },
        { name: 'minValue', type: 'int', useNull: true },
        { name: 'maxValue', type: 'int', useNull: true },
        { name: 'minDate', type: 'datetime', useNull: true },
        { name: 'maxDate', type: 'datetime', useNull: true },
        { name: 'maxFiles', type: 'int', defaultValue: 1, useNull: true },
        { name: 'interval', type: 'float', defaultValue: 1, useNull: true },
        // Necessary calculation to convert the default-value into bytes
        { name: 'maxFileSize', type: 'int', useNull: true, defaultValue: (3 * 1024 * 1024) },
        { name: 'allowsMultipleSelection', type: 'boolean', defaultValue: false, useNull: true },
        // create Fake Property for extra fields
        { name: 'typeContainer', type: 'string', useNull: true }
    ],

    associations: [
        {
            type: 'hasMany',
            model: 'Shopware.apps.SwagCustomProducts.model.Value',
            associationKey: 'values',
            name: 'getValues'
        }, {
            type: 'hasMany',
            model: 'Shopware.apps.SwagCustomProducts.model.Price',
            associationKey: 'prices',
            name: 'getPrices'
        }
    ]
});
// {/block}
