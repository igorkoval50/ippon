//{block name="backend/newsletter_manager/store/library"}
Ext.define('Shopware.apps.NewsletterManager.store.Library', {
    /**
     * Extend for the standard ExtJS 4
     * @string
     */
    extend: 'Ext.data.Store',
    /**
     * Define the used model for this store
     * @string
     */
    model: 'Shopware.apps.NewsletterManager.model.Component',
    /**
     * Configure the data communication
     * @object
     */
    proxy: {
        /**
         * Set proxy type to ajax
         * @string
         */
        type: 'ajax',

        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        url: '{url action="library" controller="SwagNewsletter"}',

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
