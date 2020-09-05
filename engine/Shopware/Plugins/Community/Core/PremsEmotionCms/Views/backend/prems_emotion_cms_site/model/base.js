Ext.define('Shopware.apps.PremsEmotionCmsSite.model.Base', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsSite',
            detail: 'Shopware.apps.PremsEmotionCmsSite.view.detail.Base'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'name', type: 'string' },
        { name : 'position', type : 'string' },
    ],
    associations: [
        {
            relation: 'ManyToMany',
            type: 'hasMany',
            model: 'Shopware.apps.PremsEmotionCmsSite.model.Emotion',
            name: 'getEmotion',
            associationKey: 'emotions'
        },
        {
            relation: 'ManyToMany',
            type: 'hasMany',
            model: 'Shopware.apps.PremsEmotionCmsSite.model.Site',
            name: 'getSite',
            associationKey: 'sites'
        }
    ]
});