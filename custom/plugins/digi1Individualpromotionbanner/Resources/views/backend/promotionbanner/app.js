Ext.define('Shopware.apps.Promotionbanner', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.Promotionbanner',

    loadPath: '{url controller="Promotionbanner" action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.Promotionbanner',
        'detail.Window',
        'detail.Promotionbanner'
    ],

    models: [
        'Promotionbanner'
    ],
    stores: [
        'Promotionbanner'
    ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});