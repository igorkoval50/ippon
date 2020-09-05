// {block name="backend/swag_business_essentials/model/private_shopping"}
Ext.define('Shopware.apps.SwagBusinessEssentials.model.PrivateShopping', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'SwagBEPrivateShopping'
        };
    },

    fields: [
        // {block name="backend/swag_business_essentials/model/private_shopping/fields"}{/block}
        { name: 'id', type: 'integer', useNull: true },
        { name: 'customerGroup', type: 'string' },
        { name: 'activateLogin', type: 'boolean' },
        { name: 'loginControllerAction', type: 'string' },
        { name: 'registerControllerAction', type: 'string' },
        { name: 'whiteListedControllers', type: 'object' },
        { name: 'registerLink', type: 'boolean' },
        { name: 'registerGroup', type: 'string', useNull: true },
        { name: 'unlockAfterRegister', type: 'boolean' },
        { name: 'templateLogin', type: 'string' },
        { name: 'templateAfterLogin', type: 'int' },
        { name: 'redirectURL', type: 'string' },
        { name: 'loginParams', type: 'object' },
        { name: 'registerParams', type: 'object' },

        // fake field for backend components to generate info text
        { name: 'infoTextField', type: 'string' }
    ],

    associations: [{
        relation: 'ManyToMany',
        type: 'hasMany',
        model: 'Shopware.apps.SwagBusinessEssentials.model.Controllers',
        name: 'getWhiteListedControllers',
        associationKey: 'whiteListedControllers'
    }, {
        relation: 'OneToMany',
        type: 'hasMany',
        model: 'Shopware.apps.SwagBusinessEssentials.model.Params',
        name: 'getLoginParams',
        associationKey: 'loginParams'
    }, {
        relation: 'OneToMany',
        type: 'hasMany',
        model: 'Shopware.apps.SwagBusinessEssentials.model.Params',
        name: 'getRegisterParams',
        associationKey: 'registerParams'
    }],

    getReloadExtraParams: function() {
        return { };
    }
});
// {/block}
