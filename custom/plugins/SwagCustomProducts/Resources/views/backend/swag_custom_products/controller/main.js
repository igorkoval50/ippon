// {block name="backend/swag_custom_products/controller/main"}
Ext.define('Shopware.apps.SwagCustomProducts.controller.Main', {
    extend: 'Enlight.app.Controller',

    /**
     * @type { Shopware.window.Listing }
     */
    mainWindow: null,

    newProduct: null,

    /**
     * Adds events, loads stores and creates the main window for this plugin.
     */
    init: function () {
        var me = this,
            openWindows = Ext.ComponentQuery.query('swag-custom-products-window');

        if (openWindows.length > 0) {
            Ext.Array.each(openWindows, function (windowInstance) {
                windowInstance.destroy();
            });
        }

        me.createMainWindow();

        me.callParent(arguments);

        me.articleModuleHandling();

        me.subscribeEvents();
    },

    /**
     * this method handles the init from the article module.
     */
    articleModuleHandling: function () {
        var me = this;

        // Support for directly opening a new custom product with a product association or
        // directly opening an existing custom product.
        if (me.subApplication.hasOwnProperty('state') && me.subApplication.hasOwnProperty('params')) {
            var params = me.subApplication.params,
                state = me.subApplication.state;

            if (state === 'newCustomProduct') {
                me.openNewCustomProductWithAssociatedProduct(params.product);
            } else if (state === 'existingCustomProduct') {
                me.openExistingCustomProduct(params.customProduct);
            }
        }
    },

    /**
     * Subscribe events.
     */
    subscribeEvents: function () {
        var me = this;

        // on Successfully save we reload the record and reopen the window for a clean record and view.
        Shopware.app.Application.on('template-save-successfully', function (controller, data, window, record) {
            record.reload({
                callback: function () {
                    var grid = window.down('swag-custom-products-option-list'),
                        options = grid.getStore(),
                        articleTab;

                    me.currentHandleRecord = record;

                    options.commitChanges();
                    options.each(function (option) {
                        if (option.get('couldContainValues')) {
                            option.getValues().commitChanges();
                        }
                    });

                    articleTab = window.down('template-view-detail-article');
                    articleTab.getStore().proxy.setExtraParam('templateId', record.get('id'));

                    if (me.newProduct) {
                        articleTab.addArticleToStore(me.newProduct);
                        me.newProduct = null;
                    }

                    articleTab.tabConfig = { };
                    articleTab.setDisabled(false);
                    articleTab.getView().refresh();
                }
            });
        });
    },

    /**
     * Creates the main window which displays the template listing
     *
     * @return { Enlight.app.Window }
     */
    createMainWindow: function () {
        var me = this;

        me.mainWindow = me.getView('overview.Window').create().show();

        return me.mainWindow;
    },

    /**
     * Provides a new preset to the user with a product association.
     *
     * @param { Object } product
     * @returns void
     */
    openNewCustomProductWithAssociatedProduct: function (product) {
        var me = this,
            gridPanel = me.mainWindow.gridPanel,
            record = gridPanel.createNewRecord(),
            productDetails = product.getMainDetailStore.data.get(0),
            productModel = Ext.create('Shopware.apps.SwagCustomProducts.model.Article', product.raw);

        productModel.set('number', productDetails.get('number'));

        me.newProduct = productModel;

        gridPanel.fireEvent(gridPanel.eventAlias + '-add-item', gridPanel, record);
    },

    /**
     * Opens up an existing custom product preset to start editing right away.
     *
     * @param { Object } customProduct
     */
    openExistingCustomProduct: function (customProduct) {
        var me = this,
            gridPanel = me.mainWindow.gridPanel,
            controller = gridPanel.controller,
            record = Ext.create('Shopware.apps.SwagCustomProducts.model.Template');

        record.set(customProduct);

        // We're reloading the record to fetch all necessary data using the associated proxy.
        record.reload({
            callback: function (refreshedRecord) {
                controller.createDetailWindow(
                    refreshedRecord,
                    gridPanel.getConfig('detailWindow')
                );
            }
        });
    }
});
// {/block}
