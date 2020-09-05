
Ext.define('Shopware.apps.PremsEmotionCmsSupplier', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.PremsEmotionCmsSupplier',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.Base',

        'detail.Base',
        'detail.Window',
        'detail.Emotion',
        'detail.Supplier',

    ],

    models: [ 'Base', 'Emotion', 'Supplier'],
    stores: [ 'Base' ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});