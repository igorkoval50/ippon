Ext.define('Shopware.apps.PremsEmotionCmsArticle2.model.Base', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsArticle2',
            detail: 'Shopware.apps.PremsEmotionCmsArticle2.view.detail.Base'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'name', type: 'string' },
        { name : 'position', type : 'string' },
        { name : 'beforeDefault', type : 'string' },
    ],
    associations: [
        {
            relation: 'ManyToMany',
            type: 'hasMany',
            model: 'Shopware.apps.PremsEmotionCmsArticle2.model.Emotion',
            name: 'getEmotion',
            associationKey: 'emotions'
        },
        {
            relation: 'ManyToMany',
            type: 'hasMany',
            model: 'Shopware.apps.PremsEmotionCmsArticle2.model.Article',
            name: 'getArticle',
            associationKey: 'articles'
        }
    ]
});