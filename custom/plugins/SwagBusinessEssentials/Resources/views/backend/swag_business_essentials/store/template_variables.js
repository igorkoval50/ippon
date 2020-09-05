// {block name="backend/swag_business_essentials/store/template_variables"}
Ext.define('Shopware.apps.SwagBusinessEssentials.store.TemplateVariables', {
    extend: 'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'SwagBETemplateVariables'
        };
    },
    model: 'Shopware.apps.SwagBusinessEssentials.model.TemplateVariables'
});
// {/block}
