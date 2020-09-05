//{block name="backend/mag_newsletter_box/store/list"}
Ext.define('Shopware.apps.MagNewsletterBox.store.List', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'MagNewsletterBox'
        };
    },

    model: 'Shopware.apps.MagNewsletterBox.model.List'
});
//{/block}