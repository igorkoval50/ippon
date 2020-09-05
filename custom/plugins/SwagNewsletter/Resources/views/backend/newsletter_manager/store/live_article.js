//{block name="backend/newsletter_manager/store/live_article"}
Ext.define('Shopware.apps.NewsletterManager.store.liveArticle', {
    /**
     * Define that this component is an extension of the Ext.data.Store
     */
    extend: 'Ext.data.Store',

    /**
     * Define the used model for this store
     * @string
     */
    model: 'Shopware.apps.NewsletterManager.model.liveArticle',

    /**
     * Define how much rows loaded with one request
     */
    pageSize: 10,

    /**
     * Auto load the store after the component
     * is initialized
     * @boolean
     */
    autoLoad: false,

    /**
     * Enable remote sorting
     */
    remoteSort: true,

    /**
     * Enable remote filtering
     */
    remoteFilter: true
});
//{/block}
