Ext.define('Shopware.apps.PremsEmotionCmsArticle2.model.Emotion', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            related: 'Shopware.apps.PremsEmotionCmsArticle2.view.detail.Emotion'
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

