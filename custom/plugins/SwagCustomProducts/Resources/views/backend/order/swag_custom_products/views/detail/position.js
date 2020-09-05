// {namespace name="backend/order/main"}

// {block name="backend/order/swag_custom_products/views/list/list"}

Ext.define('Shopware.apps.Order.view.detail.Position-SwagCustomProducts', {
    override: 'Shopware.apps.Order.view.detail.Position',

    /** @string Base64 encoded plugin icon */
    customProductsIcon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABb0lEQVR4AcWQM2AnURCHc74rz7ZvvRsnTdCmi23bduqg72Lbtm3b5iF4maDf969ujff75puR+i9bCfX2dTH1ZiCfeDkrcbiC/vC2gn4/VUy9RQDokChcxXx8X818mgMIAoPRPOLlc+xwDfOZaGC/rtcyn1E5/X6oiHz9CDtcx36hW7gf243cNwQGY2XUuyfY4Ub2m1aHQOy38b9QPft1Gqo/xVVW7xLI8X4ZFnULFGrmvi/CDMR77kqwvAnnc9COGpEV0AAAAHQCQFY0PJjjoTCU5704mO1+0JfmqNrK/4y9MGjivmdgaY8U+q0O5Xr1DGS5VfZnOB/0pNgqw7TNCsnXj0XDw3ne+qPFgScQJuDsAADqSbHbh3buY1b3bxzO9ykDfQ04z64Bq1hh0H4wXhryZyjX0whMtgCArgFRuL3HjZUE748W+R+ABboA9KU7NQNAXH+0KCBmsjLydKI8DMHzP7AZHch09QeD2zjFzwHQ38AXXf7plQAAAABJRU5ErkJggg==',

    /** @string URL which will be used to request the configuration upon a user interaction */
    getConfigUrl: '{url controller="SwagCustomProductsExtensions" action="getConfigurationByHash"}',

    /**
     * Creates the position grid and sets up the necessary event listeners to get the grouping feature working.
     *
     * @override
     * @returns { Shopware.order.position.grid }
     */
    createPositionGrid: function() {
        var me = this;

        me.rowEditor = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 2,
            autoCancel: true
        });

        me.orderPositionGrid = Ext.create('Shopware.order.position.grid', {
            name: 'order-position-grid',
            store: me.record.getPositions(),
            plugins: [ me.rowEditor, {
                ptype: 'grid-attributes',
                table: 's_order_details_attributes',
                createActionColumn: false
            } ],
            style: {
                borderTop: '1px solid #A4B5C0'
            },
            viewConfig: {
                enableTextSelection: false
            },
            features: [ me.createGroupingFeature() ],
            tbar: me.createGridToolbar(),
            selModel: me.getGridSelModel(),
            getColumns: function() {
                return me.getColumns(this);
            }
        });

        me.orderPositionGrid.on('reconfigure', function(grid, store) {
            me.updateGridGrouping(store);
        });

        me.orderPositionGrid.on('afterrender', function() {
            me.updateGridGrouping(me.record.getPositions());
        });

        return me.orderPositionGrid;
    },

    /**
     * Enables / disables the grouping features depending if the product has a custom product.
     *
     * @param { Ext.data.Store } store
     * @returns void
     */
    updateGridGrouping: function(store) {
        var me = this;

        try {
            if (me.hasCustomProducts(store)) {
                me.orderPositionGrid.groupingFeature.enable();
            } else {
                me.orderPositionGrid.groupingFeature.disable();
            }
            store.group('swag_custom_products_configuration_hash', 'DESC');
        } catch (e) {
        }
    },

    /**
     * Creates the columns for the order position grid.
     *
     * @param { Ext.grid.Panel } grid
     * @returns { Array }
     */
    getColumns: function(grid) {
        var me = this,
            overridden = me.callOverridden(arguments),
            actionColumn;

        Ext.each(overridden, function (column) {
            if (column.xtype === 'actioncolumn') {
                actionColumn = column;
            }
        });

        Ext.each(overridden, function(column) {
            if (column.dataIndex === 'articleName' || column.dataIndex === 'articleNumber') {
                column.renderer = me.nameRenderer;
            }
        });

        Ext.each(actionColumn.items, function(item) {
            if (item.action === 'openArticle') {
                item.getClass = me.getOpenArticleIcon;
            }
        });
        actionColumn.items.push(me.createOpenCustomProductAttributesColumn());
        actionColumn.width = actionColumn.width + 30;

        return overridden;
    },

    /**
     * @param { string } el
     * @param { object } meta
     * @param { Ext.data.Model } record
     * @returns { string }
     */
    getOpenArticleIcon: function(el, meta, record) {
        if (record.get('mode') !== 0) {
            return 'x-hidden';
        }
    },

    /**
     * Creates the icon to open a custom product in the action column of the grid.
     *
     * @returns { Object }
     */
    createOpenCustomProductAttributesColumn: function() {
        var me = this;

        return {
            icon: me.customProductsIcon,
            action: 'openCustomProductsAttributes',
            tooltip: '{s name="grid/tooltipShow"}{/s}',

            getClass: function(value, metadata, record) {
                if (record.raw['swag_custom_products_hide_details']) {
                    return 'x-hidden';
                }
            },

            handler: function (comp, rowIdx, colIdx, column, event, record) {
                var hash = record.get('swag_custom_products_configuration_hash');

                me.requestCustomProductConfiguration(hash);
            }
        };
    },

    /**
     * Requests the custom products configuration for the specific hash
     *
     * @param { String } hash
     * @returns void
     */
    requestCustomProductConfiguration: function(hash) {
        var me = this;

        me.setLoading(true);

        Ext.Ajax.request({
            url: me.getConfigUrl,
            params: {
                hash: hash
            },
            success: Ext.bind(me.onSuccessCustomProductConfiguration, me),
            failure: Ext.bind(me.onFailureCustomProductConfiguration, me)
        });
    },

    /**
     * Success callback of the AJAX request which gets the custom products configuration.
     * @param { XMLHttpRequest } response
     * @returns void
     */
    onSuccessCustomProductConfiguration: function(response) {
        var me = this,
            responseData = response.responseText;

        me.setLoading(false);

        responseData = Ext.JSON.decode(responseData);

        Ext.create('Shopware.apps.Order.views.detail.SwagCustomProductsDetails', {
            customProductsData: responseData.data
        });
    },

    /**
     * Failure callback of the AJAX request which gets the custom products configuration
     *
     * @param { XMLHttpRequest } response
     * @returns void
     */
    onFailureCustomProductConfiguration: function(response) {
        var me = this,
            responseData = response.responseText;

        me.setLoading(false);

        Shopware.Msg.createStickyGrowlMessage({
            title: '{s name="failure/message"}{/s}',
            text: Ext.util.Format.stripTags(responseData),
            width: 450
        });
    },

    /**
     * Name column renderer
     *
     * @param { String } value
     * @param { Object } meta
     * @param { Ext.data.Model } record
     * @returns { String }
     */
    nameRenderer: function(value, meta, record) {
        if (record.get('swag_custom_products_configuration_hash') && record.get('mode') == 0) {
            return '<strong>' + value + '</strong>';
        }
        return value;
    },

    /**
     * Checks if the product is a custom product.
     *
     * @param { Ext.data.Store } store
     * @returns { Boolean }
     */
    hasCustomProducts: function(store) {
        var found = false;

        store.each(function(item) {
            if (item.get('swag_custom_products_configuration_hash').length > 0) {
                found = true;
            }
        });
        return found;
    },

    /**
     * Creates the grouping feature for the order position slide out menu in the order overview list.
     *
     * @returns { Ext.grid.feature.Grouping }
     */
    createGroupingFeature: function() {
        var me = this;

        me.groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
            groupHeaderTpl: new Ext.XTemplate(
                '{literal}{name:this.formatName}{[this.getName(values.rows, values.name)]}{/literal}',
                {
                    getName: function(values, hash) {
                        if (hash.length <= 0) {
                            return;
                        }

                        var name = '';
                        Ext.each(values, function(value) {
                            if (value.data.mode === 0) {
                                name = value.data.articleName;
                                return false;
                            }
                        });
                        return ': ' + name;
                    },
                    formatName: function(hash) {
                        if (hash.length > 0) {
                            return '{s name="CustomProductGroupingHeader"}{/s}';
                        }
                        return '{s name="OtherPositions"}{/s}';
                    }
                }
            )
        });
        return me.groupingFeature;
    }
});

// {/block}
