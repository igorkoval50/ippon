// {block name="backend/swag_business_essentials/store/register_templates"}
Ext.define('Shopware.apps.SwagBusinessEssentials.store.RegisterTemplates', {
    extend: 'Ext.data.Store',

    autoLoad: true,
    model: 'Shopware.apps.Base.model.CustomerGroup',

    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="SwagBusinessEssentials" action="getRegisterTemplates"}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
// {/block}
