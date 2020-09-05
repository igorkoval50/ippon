
Ext.define('Shopware.apps.PremsEmotionCmsSite', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.PremsEmotionCmsSite',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.Base',

        'detail.Base',
        'detail.Window',
        'detail.Emotion',
        'detail.Site',

    ],

    models: [ 'Base', 'Emotion', 'Site'],
    stores: [ 'Base' ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});