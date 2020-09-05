//{block name="backend/newsletter_manager/store/mailing"}
Ext.define('Shopware.apps.NewsletterManager.store.Mailing', {
    extend: 'Ext.data.Store',
    // Do not load data, when not explicitly requested
    autoLoad: false,
    model: 'Shopware.apps.NewsletterManager.model.Mailing',
    remoteFilter: true,
    remoteSort: true,

    pageSize: 25,

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
            read: '{url controller=SwagNewsletter action="listNewsletters"}',
            destroy: '{url controller=NewsletterManager action="deleteNewsletter" targetField=newsletter }'
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
