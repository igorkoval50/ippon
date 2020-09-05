// {block name="backend/swag_custom_products/view/option/types/checkbox"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Checkbox', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    alias: 'swag-custom-products-option-types-checkbox',

    /**
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(me.createValueGrid());

        return items;
    },

    /**
     * @returns { Shopware.apps.SwagCustomProducts.view.components.ValueGrid }
     */
    createValueGrid: function () {
        var me = this;

        return Ext.create('Shopware.apps.SwagCustomProducts.view.components.ValueGrid', {
            parent: me,
            store: me.record.getValues()
        });
    }
});
// {/block}
