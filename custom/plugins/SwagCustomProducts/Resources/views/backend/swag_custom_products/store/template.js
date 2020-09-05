
// {block name="backend/swag_custom_products/store/template"}
Ext.define('Shopware.apps.SwagCustomProducts.store.Template', {
    /**
     * extends from the standard ExtJs store class
     */
    extend: 'Shopware.store.Listing',

    /**
     * the model which belongs to the store
     */
    model: 'Shopware.apps.SwagCustomProducts.model.Template',

    configure: function () {
        return {
            controller: 'SwagCustomProducts'
        };
    }
});
// {/block}
