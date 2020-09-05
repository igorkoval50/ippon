// {block name="backend/swag_business_essentials/model/controllers"}
Ext.define('Shopware.apps.SwagBusinessEssentials.model.Controllers', {
    extend: 'Ext.data.Model',

    idProperty: 'key',

    fields: [
        // {block name="backend/swag_business_essentials/model/controllers/fields"}{/block}
        { name: 'name', type: 'string' },
        { name: 'key', type: 'string' }
    ],

    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="SwagBEPrivateShopping" action="getControllers"}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
// {/block}
