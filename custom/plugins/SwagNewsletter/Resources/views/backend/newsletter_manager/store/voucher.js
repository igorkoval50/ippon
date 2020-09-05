//{namespace name=backend/newsletter_manager/store/voucher}
//{block name="backend/newsletter_manager/store/voucher"}
Ext.define('Shopware.apps.NewsletterManager.store.Voucher', {
    extend: 'Ext.data.Store',
    // Do not load data, when not explicitly requested
    autoLoad: false,
    model: 'Shopware.apps.NewsletterManager.model.Voucher',

    /**
     * Configure the data communication
     * @object
     */
    proxy: {
        type: 'ajax',

        /**
         * Configure the url mapping
         * @object
         */
        api: {
            read: '{url controller=SwagNewsletter action="getVoucher"}'
        },

        /**
         * Configure the data reader
         * @object
         */
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
//{/block}
