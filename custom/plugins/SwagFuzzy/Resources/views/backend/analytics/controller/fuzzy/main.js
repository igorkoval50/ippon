
// {namespace name=backend/analytics/view/fuzzy}
// {block name="backend/analytics/controller/main"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.Analytics.controller.fuzzy.Main', {
    override: 'Shopware.apps.Analytics.controller.Main',

    constructor: function () {
        this.refs = (this.refs || []).concat([
            { ref: 'searchField', selector: 'analytics-toolbar textfield[name=searchTerm]' }
        ]);

        this.callParent(arguments);
    },

    /**
     * Loads the chart and table for the selected statistic.
     * If one of the components is not present, the layout switch button will be hidden.
     * Shows/hides the shop combobox depending of the multiShop parameter of the statistic.
     *
     * @param store
     * @param record
     */
    renderDataOutput: function (store, record) {
        var me = this,
            chartId = 'widget.analytics-chart-' + record.data.id,
            tableId = 'widget.analytics-table-' + record.data.id,
            panel = me.getPanel(),
            layout = true,
            fromValue = me.getFromField().value,
            toValue = me.getToField().value,
            navRecord = record.getId(),
            searchField = me.getSearchField(),
            searchTerm = searchField.getValue().toString();

        me.callParent(arguments);

        // only show the search field on searches pages
        searchField.hide();
        if (navRecord === 'searches_without_results' || navRecord === 'search') {
            searchField.show();
        }

        // Remove all previous inserted charts / tables
        Ext.suspendLayouts();
        panel.removeAll(true);
        panel.setLoading(true);

        Ext.apply(store.getProxy().extraParams, {
            node: 'root'
        });
        if (Ext.typeOf(fromValue) === 'date') {
            store.getProxy().extraParams.fromDate = fromValue;
        }
        if (Ext.typeOf(toValue) === 'date') {
            store.getProxy().extraParams.toDate = toValue;
        }
        if (me.getShopSelection() && me.getShopSelection().getValue()) {
            store.getProxy().extraParams.selectedShops = me.getShopSelection().getValue().toString();
        }
        // add searchTerm to store
        delete store.getProxy().extraParams.searchTerm;
        if (searchTerm) {
            store.getProxy().extraParams.searchTerm = searchTerm;
        }

        me.currentStore = store;
        me.currentNavigationItem = record;

        if (me.getNavigation()) {
            me.getNavigation().setLoading(true);
        }

        store.load({
            callback: function (result, request) {
                if (me.getNavigation()) {
                    me.getNavigation().setLoading(false);
                }
                panel.setLoading(false);

                if (request.success === false) {
                    return;
                }

                if (Ext.ClassManager.getNameByAlias(chartId)) {
                    var chart = Ext.create(chartId, {
                        store: store,
                        shopStore: me.shopStore,
                        shopSelection: me.getShopSelection().value
                    });

                    panel.add(chart);
                } else {
                    layout = false;
                }

                if (Ext.ClassManager.getNameByAlias(tableId)) {
                    var table = Ext.create(tableId, {
                        store: store,
                        shopStore: me.shopStore,
                        shopSelection: me.getShopSelection().value
                    });
                    panel.add(table);
                } else {
                    layout = false;
                }

                if (record.raw.multiShop) {
                    me.getShopSelection().show();
                } else {
                    me.getShopSelection().hide();
                }

                var activeItem;
                if (!layout) {
                    me.getLayoutButton().hide();
                    activeItem = 0;
                } else {
                    me.getLayoutButton().show();
                    activeItem = me.getLayoutButton().getActiveItem();

                    activeItem = activeItem.layout === 'table' ? 0 : 1;
                }

                panel.getLayout().setActiveItem(activeItem);

                Ext.resumeLayouts(true);
            }
        });
    }
});
// {/block}
