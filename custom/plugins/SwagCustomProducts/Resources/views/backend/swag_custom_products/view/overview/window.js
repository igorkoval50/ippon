// {namespace name="backend/swag_custom_products/overview/window"}
// {block name="backend/swag_custom_products/overview/window"}
Ext.define('Shopware.apps.SwagCustomProducts.view.overview.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.swag-custom-products-window',

    layout: 'fit',
    height: 660,
    width: 800,

    snippets: {
        title: '{s name="title"}CustomProducts - Template overview{/s}'
    },

    /**
     * @return { Object }
     */
    configure: function () {
        var me = this;
        me.title = me.snippets.title;

        return {
            listingGrid: 'Shopware.apps.SwagCustomProducts.view.overview.List',
            listingStore: 'Shopware.apps.SwagCustomProducts.store.Template'
        };
    },

    initComponent: function () {
        var me = this;

        me.callParent(arguments);
    }
});
// {/block}
