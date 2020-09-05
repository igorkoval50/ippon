// {block name="backend/swag_business_essentials/model/private_register"}
Ext.define('Shopware.apps.SwagBusinessEssentials.model.PrivateRegister', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'SwagBEPrivateRegister'
        };
    },

    fields: [
        // {block name="backend/swag_business_essentials/model/private_register/fields"}{/block}
        { name: 'id', type: 'integer', useNull: true },
        { name: 'customerGroup', type: 'string' },
        { name: 'allowRegister', type: 'boolean' },
        { name: 'requireUnlock', type: 'boolean' },
        { name: 'assignGroupBeforeUnlock', type: 'string' },
        { name: 'registerTemplate', type: 'string' },
        { name: 'emailTemplateDeny', type: 'string' },
        { name: 'emailTemplateAllow', type: 'string' },
        { name: 'displayLink', type: 'string' },
        { name: 'link', type: 'string' },

        // fake field for backend components to generate info text
        { name: 'infoTextField', type: 'string' }
    ],

    getReloadExtraParams: function () {
        return { };
    }
});
// {/block}
