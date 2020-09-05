// {block name="backend/swag_business_essentials/model/business_essentials"}
Ext.define('Shopware.apps.SwagBusinessEssentials.model.BusinessEssentials', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'SwagBusinessEssentials',
            detail: 'Shopware.apps.SwagBusinessEssentials.view.detail.BusinessEssentials'
        };
    },

    fields: [
        // {block name="backend/swag_business_essentials/model/business_essentials/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'firstLogin', type: 'date' },
        { name: 'firstname', type: 'string' },
        { name: 'lastname', type: 'string' },
        { name: 'company', type: 'string' },
        { name: 'validation', type: 'string' },
        { name: 'subshopName', type: 'string' },
        { name: 'toCustomerGroup', type: 'string' },
        { name: 'customer', type: 'string' }
    ]
});
// {/block}
