//

// {block name="backend/swag_custom_products/model/template"}
Ext.define('Shopware.apps.SwagCustomProducts.model.Template', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagCustomProducts',
            detail: 'Shopware.apps.SwagCustomProducts.view.detail.Detail'
        };
    },

    fields: [
        // {block name="backend/swag_custom_products/model/template/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'internalName', type: 'string', useNull: false },
        { name: 'displayName', type: 'string', useNull: true },
        { name: 'description', type: 'string', useNull: true },
        { name: 'mediaId', type: 'int', useNull: true },
        { name: 'stepByStepConfigurator', type: 'boolean' },
        { name: 'active', type: 'boolean' },
        { name: 'confirmInput', type: 'boolean' },
        { name: 'productCount', type: 'int' },
        { name: 'optionCount', type: 'int' },
        { name: 'variantsOnTop', type: 'boolean' }
    ],

    associations: [
        {
            type: 'hasMany',
            relation: 'ManyToOne',

            field: 'mediaId',
            model: 'Shopware.apps.Base.model.Media',
            name: 'getMedia',
            associationKey: 'media'
        }, {
            type: 'hasMany',
            relation: 'OneToMany',

            model: 'Shopware.apps.SwagCustomProducts.model.Option',
            name: 'getOptions',
            associationKey: 'options'
        }
    ]
});
// {/block}
