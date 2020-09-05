Ext.define('Shopware.apps.PremsEmotionCmsArticle2.model.Article', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            related: 'Shopware.apps.PremsEmotionCmsArticle2.view.detail.Article'
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

