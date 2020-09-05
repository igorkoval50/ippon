//{namespace name=backend/newsletter_manager/view/main}

/**
 * This panel wraps the default component and allows you to add a collapsable sidebar
 */
//{block name="backend/newsletter_manager/view/components/text"}
Ext.define('Shopware.apps.NewsletterManager.view.components.Text', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.newsletter-components-text',
    layout: 'border',

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

        me.items = [
            {
                xtype: 'newsletter-components-base',
                region: 'center',
                settings: me.settings
            },
            {
                xtype: 'newsletter-components-text-east',
                region: 'east',
                settings: me.settings
            }
        ];

        me.callParent(arguments);
    }
});
//{/block}
