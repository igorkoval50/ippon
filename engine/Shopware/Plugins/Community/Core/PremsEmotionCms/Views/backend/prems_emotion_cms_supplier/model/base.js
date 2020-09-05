Ext.define('Shopware.apps.PremsEmotionCmsSupplier.model.Base', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsSupplier',
            detail: 'Shopware.apps.PremsEmotionCmsSupplier.view.detail.Base'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'shopId', type: 'int', defaultValue: 1},
        { name : 'name', type: 'string' },
        { name : 'position', type : 'string' },
    ],
    associations: [
        {
            relation: 'ManyToMany',
            type: 'hasMany',
            model: 'Shopware.apps.PremsEmotionCmsSupplier.model.Emotion',
            name: 'getEmotion',
            associationKey: 'emotions'
        },
        {
            relation: 'ManyToMany',
            type: 'hasMany',
            model: 'Shopware.apps.PremsEmotionCmsSupplier.model.Supplier',
            name: 'getSupplier',
            associationKey: 'suppliers'
        },
        {
            relation: 'ManyToOne',
            field: 'shopId',
            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Shop',
            name: 'getShop',
            associationKey: 'shop'
        }
    ]
});