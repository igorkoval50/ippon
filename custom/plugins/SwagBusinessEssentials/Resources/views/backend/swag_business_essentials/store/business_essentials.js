// {block name="backend/swag_business_essentials/store/business_essentials"}
Ext.define('Shopware.apps.SwagBusinessEssentials.store.BusinessEssentials', {
    extend: 'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'SwagBusinessEssentials'
        };
    },
    model: 'Shopware.apps.SwagBusinessEssentials.model.BusinessEssentials'
});
// {/block}
