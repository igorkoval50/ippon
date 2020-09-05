// {namespace name="backend/article/swag_custom_products"}

// {block name="backend/article/view/detail/window"}

// {$smarty.block.parent}

Ext.define('Shopware.apps.Article.views.Window-SwagCustomProducts', {
    override: 'Shopware.apps.Article.view.detail.Window',

    /**
     * Hooks into the method to inject the custom products fieldset into the first tab of the product module.
     *
     * @returns { Ext.container.Container }
     */
    createBaseTab: function () {
        var me = this,
            overridden = me.callOverridden(arguments),
            fieldset;

        me.customProductFieldset = fieldset = Ext.create('Shopware.apps.Article.views.Fieldset-SwagCustomProducts');
        me.detailForm.insert(me.detailForm.items.length - 1, fieldset);

        // Register a listener to get notified that the user has changed the product using the split view
        me.subApplication.on('ProductModule:storesChanged', function(product) {
            fieldset.fireEvent('productHasChanged', product);
        });

        me.on('saveArticle', function(window, product) {
            fieldset.fireEvent('productHasChanged', product);
        });

        Shopware.app.Application.on('SwagCustomProducts-onSave', function() {
            fieldset.requestCustomProducts(me.article);
        });

        return overridden;
    },

    /**
     * Due to the nature of the product module, we have to hook the method to notify the custom product fieldset
     * that the product has changed and therefore the fieldset has to request the custom product data for the
     * selected product again.
     *
     * @returns void
     */
    onStoresLoaded: function() {
        var me = this;

        me.callOverridden(arguments);
        me.customProductFieldset.fireEvent('productHasChanged', me.article);
    }
});

// {/block}
