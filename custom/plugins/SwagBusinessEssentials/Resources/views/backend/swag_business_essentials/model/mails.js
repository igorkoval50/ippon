// {block name="backend/swag_business_essentials/model/mails"}
Ext.define('Shopware.apps.SwagBusinessEssentials.model.Mails', {
    extend: 'Ext.data.Model',

    fields: [
        // {block name="backend/swag_business_essentials/model/mails/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' }
    ],

    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="EntitySearch" action="search" model="\\Shopware\\Models\\Mail\\Mail"}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
// {/block}
