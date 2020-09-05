Ext.define('Shopware.apps.PremsEmotionCmsSite.model.Emotion', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            related: 'Shopware.apps.PremsEmotionCmsSite.view.detail.Emotion'
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

