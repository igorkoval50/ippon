Ext.define('Shopware.apps.PremsEmotionCmsSupplier.model.Supplier', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            related: 'Shopware.apps.PremsEmotionCmsSupplier.view.detail.Supplier'
        }
    },

    /**
     * unique id
     * @int
     */
    idProperty:'id',

    /**
     * The fields used for this model
     * @array
     */
    fields:[
        { name : 'id', type:'int' },
        { name : 'name', type:'string' }
    ]
});

