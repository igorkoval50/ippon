//{block name="backend/mag_newsletter_box/view/list/window"}
Ext.define('Shopware.apps.MagNewsletterBox.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.log-main-list',
    height: 450,
    title : '{s namespace="backend/plugins/mag_newsletter_box" name="window_title"}{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.MagNewsletterBox.view.list.List',
            listingStore: 'Shopware.apps.MagNewsletterBox.store.List',
            controller: 'MagNewsletterBox'
        };
    }
});
//{/block}