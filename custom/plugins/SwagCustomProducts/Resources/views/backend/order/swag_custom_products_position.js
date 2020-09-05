
//{block name="backend/order/model/position/fields"}

//{$smarty.block.parent}

{ name: 'swag_custom_products_configuration_hash', type:'string' },
{ name: 'swag_custom_products_mode', type:'int' },

// {/block}



//{block name="backend/order/view/list/position"}

//{$smarty.block.parent}

//{namespace name=backend/order/main}

Ext.define('Shopware.apps.Order.view.list.Position-SwagCustom-Products', {
    override: 'Shopware.apps.Order.view.list.Position',
    autoScroll: false,

    initComponent: function() {
        var me = this;

        me.features = [me.createGroupingFeature()];

        me.on('reconfigure', function(grid, store) {
            me.updateGridGrouping(store);
        });

        me.on('afterrender', function() {
            me.updateGridGrouping(me.getStore());
        });

        me.callParent(arguments);
    },

    updateGridGrouping: function(store) {
        var me = this;

        try {
            if (me.hasCustomProducts(store)) {
                me.groupingFeature.enable();
            } else {
                me.groupingFeature.disable()
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

        Ext.Array.each(overridden, function (column) {
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

    nameRenderer: function(value, meta, record) {
        if (record.get('swag_custom_products_configuration_hash') && record.get('mode') == 0) {
            return '<strong>'+value+'</strong>'
        }
        return value;
    },

    hasCustomProducts: function(store) {
        var found = false;

        store.each(function(item) {
            if (item.get('swag_custom_products_configuration_hash').length > 0) {
                found = true;
            }
        });
        return found;
    },

    createGroupingFeature: function() {
        var me = this;

        me.groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
            groupHeaderTpl: new Ext.XTemplate(
                '{literal}{name:this.formatName}{[this.getName(values.rows, values.name)]}{/literal}',
                {
                    getName: function(values,hash) {
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

//{/block}
