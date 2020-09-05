// {block name="backend/swag_business_essentials/model/template_variables"}
Ext.define('Shopware.apps.SwagBusinessEssentials.model.TemplateVariables', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'SwagBETemplateVariables',
            detail: 'Shopware.apps.SwagBusinessEssentials.view.template_variables.Detail'

        };
    },

    fields: [
        // {block name="backend/swag_business_essentials/model/template_variables/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'variable', type: 'string', useNull: true },
        { name: 'description', type: 'string', useNull: true },

        // fake field for backend components to generate info text
        { name: 'assignedCustomerGroups', type: 'string' }
    ],

    associations: [{
        relation: 'ManyToMany',

        type: 'hasMany',
        model: 'Shopware.apps.SwagBusinessEssentials.model.CustomerGroup',
        name: 'customerGroups',
        associationKey: 'customerGroups'
    }]
});
// {/block}
