
Ext.define('Shopware.apps.StuttSeoRedirects.model.Redirect', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'StuttSeoRedirects',
            detail: 'Shopware.apps.StuttSeoRedirects.view.detail.Redirect'
        };
    },


    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'active', type: 'boolean' },
        { name : 'oldUrl', type: 'string' },
        { name : 'newUrl', type: 'string' },
        { name : 'overrideShopUrl', type: 'boolean' },
        { name : 'temporaryRedirect', type: 'boolean' },
        { name : 'externalRedirect', type: 'boolean' },
        { name : 'shop_id', type: 'int', useNull: true },
        { name : 'shopName', type: 'string', useNull: true },
        { name : 'gone', type: 'boolean' }
    ],

    associations: [{
        relation: 'ManyToOne',
        field: 'shop_id',
        useNull: true,

        type: 'hasMany',
        model: 'Shopware.apps.Base.model.Shop',
        name: 'getShop',
        associationKey: 'shop'
    }]
});

