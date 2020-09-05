// {block name="backend/swag_custom_products/view/option/types/imageselect"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Imageselect', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    alias: 'swag-custom-products-option-types-checkbox',

    /**
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(me.createAllowsMultipleSelectionField());
        items.push(me.createValueGrid());

        return items;
    },

    /**
     * Overwrites the method which will be called when the record was loaded successfully. The method sets the value
     * for our fields.
     *
     * @returns void
     */
    loadRecord: function() {
        var me = this;

        me.callParent(arguments);

        me.allowsMultipleSelectionField.setValue(me.record.get('allowsMultipleSelection'));
    },

    /**
     * @returns { Shopware.apps.SwagCustomProducts.view.components.ValueGrid }
     */
    createValueGrid: function () {
        var me = this;

        return Ext.create('Shopware.apps.SwagCustomProducts.view.components.ValueGrid', {
            parent: me,
            useImagePicker: true,
            store: me.record.getValues(),
            type: me.record.get('type')
        });
    }
});
// {/block}
