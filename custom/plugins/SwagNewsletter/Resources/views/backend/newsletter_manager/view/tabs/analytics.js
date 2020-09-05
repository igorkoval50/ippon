//{namespace name="backend/swag_newsletter/main"}
//{block name="backend/newsletter_manager/view/tabs/analytics"}
Ext.define('Shopware.apps.NewsletterManager.view.tabs.Analytics', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.newsletter-manager-tabs-analytics',
    title: '{s name=analytics}Analytics{/s}',
    layout: 'fit',
    bodyBorder: 0,
    border: false,
    defaults: {
        bodyBorder: 0
    },

    /**
     * Initializes the component, sets up toolbar and pagingbar and and registers some events
     *
     * @return void
     */
    initComponent: function () {
        var me = this;

        // Create the items of the container
        me.items = me.createTab();

        me.callParent(arguments);
    },

    /**
     * Creates the tab panel for the main window
     */
    createTab: function () {
        var me = this;

        me.tabPanel = Ext.create('Ext.tab.Panel', {
            items: me.getTabs()
        });
        return me.tabPanel;
    },

    /**
     * Creates the admin tab
     * @return Array
     */
    getTabs: function () {
        var me = this;

        return [
            {
                xtype: 'newsletter-manager-tabs-statistics',
                store: me.store
            },
            {
                xtype: 'newsletter-manager-tabs-orders',
                store: me.orderStore,
                paymentStatusStore: me.paymentStatusStore,
                orderStatusStore: me.orderStatusStore
            }
        ];
    }

});
//{/block}
