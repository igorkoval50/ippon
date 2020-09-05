// {namespace name="backend/bundle/article/view/main"}
// {block name="backend/article/view/bundle/tabs/price"}
Ext.define('Shopware.apps.Article.view.bundle.tabs.Price', {

    extend: 'Ext.grid.Panel',

    title: '{s name=prices/title}Prices{/s}',

    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets
     */
    alias: 'widget.bundle-price-listing',

    initComponent: function() {
        var me = this;
        me.registerEvents();
        me.columns = me.createColumns();
        me.tbar = me.createToolBar();
        me.plugins = [me.createCellEditor()];
        me.callParent(arguments);
    },

    /**
     * Adds the specified events to the list of events which this Observable may fire
     */
    registerEvents: function() {
        this.addEvents(
            /**
             * @Event
             * Custom component event.
             * Fired when the customer select a customer group in the toolbar combo box.
             * @param Ext.data.Model The selected record
             */
            'addPrice',

            /**
             * @Event
             * Custom component event.
             * Fired when the user clicks the delete action column item.
             * @param Ext.data.Model The row record
             */
            'deletePrice'
        );
    },

    /**
     * Creates the columns for the grid panel.
     * @return Array
     */
    createColumns: function() {
        var me = this, columns = [];

        columns.push(me.createNameColumn());
        columns.push(me.createTotalPriceColumn());
        columns.push(me.createDiscountColumn());
        columns.push(me.createEndPriceColumn());
        columns.push(me.createActionColumn());

        return columns;
    },

    /**
     * Creates the name column for the listing.
     * @return Ext.grid.column.Column
     */
    createNameColumn: function() {
        var me = this;

        return Ext.create('Ext.grid.column.Column', {
            header: '{s name=prices/customer_group_column}Customer group{/s}',
            dataIndex: 'customerGroup.name',
            flex: 1,
            renderer: me.customerGroupColumnRenderer
        });
    },

    /**
     * Creates the price column for the listing
     * @return Ext.grid.column.Column
     */
    createDiscountColumn: function() {
        var me = this, title = '';

        if (me.bundle && me.bundle.get('discountType') === 'abs') {
            title = '{s name=prices/end_price}End price{/s}';
        } else {
            title = '{s name=prices/discount_in_percent}Discount in %{/s}';
        }

        return Ext.create('Ext.grid.column.Column', {
            header: title,
            dataIndex: 'price',
            flex: 1,
            renderer: me.priceColumnRenderer,
            editor: {
                xtype: 'numberfield',
                allowBlank: false,
                decimalPrecision: 2
            }
        });
    },

    /**
     * Creates the end price column for the listing
     * @return Ext.grid.column.Column
     */
    createEndPriceColumn: function() {
        var me = this, title;

        if (me.bundle && me.bundle.get('discountType') === 'abs') {
            title = '{s name=prices/discount_in_euro}Discount value{/s}';
        } else {
            title = '{s name=prices/end_price}End price{/s}';
        }

        return Ext.create('Ext.grid.column.Column', {
            header: title,
            dataIndex: 'endPrice',
            flex: 1,
            renderer: me.endPriceColumnRenderer
        });
    },

    /**
     * Creates the price column for the listing
     * @return Ext.grid.column.Column
     */
    createTotalPriceColumn: function() {
        var me = this;

        return Ext.create('Ext.grid.column.Column', {
            header: '{s name=prices/summarized_product_price_column}Product price (summarized){/s}',
            dataIndex: 'prices.price',
            flex: 1,
            renderer: me.totalPriceColumnRenderer
        });
    },

    /**
     * Creates the action column for the listing.
     * @return { Ext.grid.column.Action }
     */
    createActionColumn: function() {
        var me = this, items;

        items = me.getActionColumnItems();

        return Ext.create('Ext.grid.column.Action', {
            items: items,
            width: items.length * 30
        });
    },

    /**
     * Creates the action column items for the listing.
     * @return { Array }
     */
    getActionColumnItems: function() {
        var me = this,
            items = [];

        items.push(me.createDeleteActionColumnItem());
        return items;
    },

    /**
     * Creates the delete action column item for the listing action column
     * @return { Object }
     */
    createDeleteActionColumnItem: function() {
        var me = this;

        return {
            iconCls: 'sprite-minus-circle-frame',
            width: 30,
            tooltip: '{s name=prices/delete_price_column}Delete price{/s}',
            handler: function(grid, rowIndex, colIndex, metaData, event, record) {
                me.fireEvent('deletePrice', [record]);
            }
        };
    },

    /**
     * Creates the tool bar for the listing component.
     * @return { Ext.toolbar.Toolbar }
     */
    createToolBar: function() {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            items: me.createToolBarItems(),
            dock: 'top'
        });
    },

    /**
     * Creates the elements for the listing toolbar.
     * @return { Array }
     */
    createToolBarItems: function() {
        var me = this, items = [];

        items.push(me.createToolBarSpacer(6));
        items.push(me.createToolBarCustomerGroupComboBox());
        return items;
    },

    /**
     * Creates a toolbar spacer with the passed width value.
     * @param width
     * @return { Ext.toolbar.Spacer }
     */
    createToolBarSpacer: function(width) {
        return Ext.create('Ext.toolbar.Spacer', {
            width: width
        });
    },

    /**
     * Creates the customer group combo box for the bundle customer group listing.
     * @return { Ext.form.field.ComboBox }
     */
    createToolBarCustomerGroupComboBox: function() {
        var me = this;

        me.customerGroupComboBox = Ext.create('Ext.form.field.ComboBox', {
            store: me.customerGroupStore,
            queryMode: 'local',
            name: 'customerGroup',
            margin: '0 0 9 0',
            displayField: 'name',
            valueField: 'id',
            fieldLabel: '{s name=prices/add_price_field}Add price{/s}',
            labelWidth: 180,
            width: 400,
            listeners: {
                select: function(combo, record) {
                    me.fireEvent('addPrice', record[0]);
                }
            }
        });
        return me.customerGroupComboBox;
    },

    /**
     * Creates the cell editor plugin for the listing component.
     * @return { Ext.grid.plugin.CellEditing }
     */
    createCellEditor: function() {
        var me = this;

        me.cellEditor = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
        });
        return me.cellEditor;
    },

    /**
     * Renderer function of the customer group column.
     * @param value
     * @param metaData
     * @param record Ext.data.Model
     */
    customerGroupColumnRenderer: function(value, metaData, record) {
        if (record.getCustomerGroup() instanceof Ext.data.Store && record.getCustomerGroup().first() instanceof Ext.data.Model) {
            return record.getCustomerGroup().first().get('name');
        }

        return '';
    },

    /**
     * Renderer function for the price column.
     * @param value
     * @param metaData
     * @param record
     */
    priceColumnRenderer: function(value, metaData, record) {
        return Ext.util.Format.number(record.get('price'));
    },

    /**
     * Renderer function for the total price column.
     * @param value
     * @param metaData
     * @param record
     */
    totalPriceColumnRenderer: function(value, metaData, record) {
        var me = this;

        var price = me.bundleController.getTotalAmountForCustomerGroup(
            record.getCustomerGroup().first(),
            me.customerGroupStore
        );
        return Ext.util.Format.number(price);
    },

    /**
     * Renderer function for the end price column.
     * @param value
     * @param metaData
     * @param record
     */
    endPriceColumnRenderer: function(value, metaData, record) {
        var me = this, discount, percentage, price;

        var totalPrice = me.bundleController.getTotalAmountForCustomerGroup(
            record.getCustomerGroup().first(),
            me.customerGroupStore
        );
        if (me.bundle.get('discountType') === 'pro') {
            discount = record.get('price');
            percentage = (100 - discount) / 100;
            price = totalPrice * percentage;
        } else {
            discount = record.get('price');
            price = totalPrice - discount;
        }

        return Ext.util.Format.number(price);
    }
});
// {/block}
