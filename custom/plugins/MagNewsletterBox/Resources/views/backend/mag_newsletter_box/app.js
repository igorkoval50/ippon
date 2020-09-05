//{block name="backend/mag_newsletter_box/app"}
Ext.define('Shopware.apps.MagNewsletterBox', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.MagNewsletterBox',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.List',
    ],

    models: [ 'List' ],
    stores: [ 'List' ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});
//{/block}