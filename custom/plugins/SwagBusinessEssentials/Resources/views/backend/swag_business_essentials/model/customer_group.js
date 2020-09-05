// {block name="backend/swag_business_essentials/model/customer_group"}
Ext.define('Shopware.apps.SwagBusinessEssentials.model.CustomerGroup', {
    extend: 'Shopware.apps.Base.model.CustomerGroup',

    configure: function() {
        return {
            related: 'Shopware.apps.SwagBusinessEssentials.view.template_variables.CustomerGroups'
        };
    }
});
// {/block}
