// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/list/filter_panel"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.list.FilterPanel', {
    extend: 'Shopware.listing.FilterPanel',
    alias: 'widget.swagBusinessEssentials-listing-filter-panel',
    width: 270,

    whiteList: null,

    filterButtonText: '{s name="BusinessEssentialsFilterPanelApplyText"}{/s}',
    infoTextSnippet: '{s name="BusinessEssentialsFilterPanelInfoText"}{/s}',

    snippets: {
        filterNames: {
            firstLogin: '{s name="DateColumnHeader"}{/s}',
            customerGroup: '{s name="CustomerGroupColumnHeader"}{/s}',
            subShopName: '{s name="ShopColumnHeader"}{/s}'
        },
        invalidKeyTitle: '{s name="FilterInvalidKeyTitle"}{/s}',
        invalidKeyMessage: '{s name="FilterInvalidKeyMessage"}{/s}',
        caller: '{s name="MainTitle"}{/s}'
    },

    configure: function() {
        var me = this;

        return {
            controller: 'SwagBusinessEssentials',
            model: 'Shopware.apps.SwagBusinessEssentials.model.BusinessEssentials',
            fields: {
                firstLogin: me.snippets.filterNames.firstLogin,
                toCustomerGroup: me.snippets.filterNames.customerGroup,
                subshopName: me.snippets.filterNames.subShopName
            }
        };
    },

    /**
     * Adds entries to the white-list for the filter-panel.
     */
    initComponent: function() {
        var me = this;

        me.whiteList = {
            firstLogin: true,
            toCustomerGroup: true,
            subshopName: true
        };

        me.callParent(arguments);
    },

    /**
     * Overwritten to only allow fields in the filter-panel being in the white-list.
     *
     * @param { Ext.data.Model } model
     * @param { Ext.data.Field } field
     * @param { string | * | null } alias
     * @param { Object } customConfig
     * @returns { Object }
     */
    createModelField: function(model, field, alias, customConfig) {
        var me = this,
            key = field.name;

        if (!me.isWhiteListed(key)) {
            return me.callParent(arguments);
        }

        return me.createExchangedFilter(me.callParent(arguments), key);
    },

    /**
     * Helper method to check if a field-key is whitelisted.
     *
     * @param { string } key
     * @returns { bool }
     */
    isWhiteListed: function(key) {
        return this.whiteList[key];
    },

    /**
     * Replaces the shop- and the customer-group field with combo-boxes instead of simple strings.
     *
     * @param { Object } filter
     * @param { string } key
     * @returns { Object }
     */
    createExchangedFilter: function(filter, key) {
        var me = this,
            config = {
                xtype: 'combobox',
                displayField: 'name',
                valueField: 'name'
            };

        switch (key) {
            case 'subshopName':
                Ext.apply(filter, config);
                filter.store = Ext.create('Shopware.apps.Base.store.Shop', {
                    filters: []
                });

                break;
            case 'toCustomerGroup':
                Ext.apply(filter, config);
                filter.store = Ext.create('Shopware.store.CustomerGroup');

                break;
            case 'firstLogin':
                break;
            default:
                Shopware.Notification.createGrowlMessage(
                    me.snippets.invalidKeyTitle,
                    me.snippets.invalidKeyMessage,
                    me.snippets.caller
                );

                break;
        }

        return filter;
    },

    /**
     * Removes the background-style from the filter-fields.
     *
     * @returns { Ext.container.Container }
     */
    createFilterFields: function() {
        var me = this,
            fieldContainer = me.callParent(arguments);

        fieldContainer.items.each(function(item) {
            item.style = 'background: inherit;';
        });

        return fieldContainer;
    }
});
// {/block}
