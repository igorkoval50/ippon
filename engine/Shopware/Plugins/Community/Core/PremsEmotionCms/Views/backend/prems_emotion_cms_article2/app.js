
Ext.define('Shopware.apps.PremsEmotionCmsArticle2', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.PremsEmotionCmsArticle2',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.Base',

        'detail.Base',
        'detail.Window',
        'detail.Emotion',
        'detail.Article',

    ],

    models: [ 'Base', 'Emotion', 'Article'],
    stores: [ 'Base' ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});