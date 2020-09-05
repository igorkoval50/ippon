Ext.define('Shopware.apps.PremsEmotionCmsSite.model.Site', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            related: 'Shopware.apps.PremsEmotionCmsSite.view.detail.Site'
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
        { name : 'name', mapping:'description', type:'string' }
    ]
});

