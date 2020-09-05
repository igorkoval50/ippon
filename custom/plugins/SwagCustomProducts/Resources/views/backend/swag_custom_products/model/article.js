
// {block name="backend/swag_custom_products/model/article"}
Ext.define('Shopware.apps.SwagCustomProducts.model.Article', {

    extend: 'Shopware.model.Article',

    configure: function () {
        return {
            related: 'Shopware.apps.SwagCustomProducts.view.detail.Article'
        };
    }
});
// {/block}
