// {block name="backend/swag_custom_products/components/optionsSelectionModel"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.OptionsSelectionModel', {
    extend: 'Ext.selection.CheckboxModel',

    /**
     * Fix an issue with the drag-and-drop plugin and the selection model on the grid.
     * @overwrite
     * @param { Ext.grid.View } view
     */
    bindComponent: function (view) {
        var me = this;

        me.views = me.views || [];
        me.views.push(view);
        me.bindStore(view.getStore(), true);

        view.on({
            itemclick: me.onRowMouseDown,
            scope: me
        });

        if (me.enableKeyNav) {
            me.initKeyNav(view);
        }
    }
});
// {/block}
