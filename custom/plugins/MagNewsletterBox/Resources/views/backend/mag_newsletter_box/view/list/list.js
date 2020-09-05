//{block name="backend/mag_newsletter_box/view/list/list"}
Ext.define('Shopware.apps.MagNewsletterBox.view.list.List', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.product-listing-grid',
    region: 'center',

    configure: function() {
        return {
            editColumn: false,
            addButton: false,
            controller: 'MagNewsletterBox'
        };
    }
});
//{/block}