// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/list/business_essentials"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.list.BusinessEssentials', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.business_essentials-listing-grid',
    region: 'center',
    addButtonText: '{s name="AcceptRequests"}{/s}',
    deleteButtonText: '{s name="RejectRequests"}{/s}',

    snippets: {
        columnHeaders: {
            firstLogin: '{s name="DateColumnHeader"}{/s}',
            name: '{s name="CustomerColumnHeader"}{/s}',
            company: '{s name="CompanyColumnHeader"}{/s}',
            customerGroup: '{s name="CustomerGroupColumnHeader"}{/s}',
            subShopName: '{s name="ShopColumnHeader"}{/s}'
        }
    },

    configure: function() {
        var me = this;

        return {
            columns: {
                firstLogin: { header: me.snippets.columnHeaders.firstLogin, flex: 1 },
                customer: {
                    xtype: 'templatecolumn',
                    header: me.snippets.columnHeaders.name,
                    flex: 3,
                    tpl: '{literal}{firstname} {lastname}{/literal}'
                },
                company: { header: me.snippets.columnHeaders.company, flex: 3 },
                toCustomerGroup: { header: me.snippets.columnHeaders.customerGroup, flex: 2 },
                subshopName: { header: me.snippets.columnHeaders.subShopName, flex: 1 }
            }
        };
    },

    /**
     * Adds an event-listener to (de-)select the "add"-button depending on the selection-length of the
     * selection-model.
     */
    initComponent: function() {
        var me = this;

        me.callParent(arguments);
        me.registerEvents();

        me.on(me.eventAlias + '-selection-changed', function(grid, selModel, selection) {
            me.addButton.setDisabled(selection.length <= 0);
        });
    },

    /**
     * @inheritDoc
     */
    registerEvents: function() {
        var me = this;

        me.callParent(arguments);

        me.addEvents(
            /**
             * Event fired when the user accepts one or multiple customers by clicking the "accept customer"-button
             * in the toolbar.
             *
             * @param { Ext.data.Model[] } Array of the selected models
             */
            'acceptCustomers',

            /**
             * Event fired when the user declines one or multiple customers by clicking the "decline customer"-button
             * in the toolbar.
             *
             * @param { Ext.data.Model[] } Array of the selected models
             */
            'declineCustomers',

            /**
             * Event fired when the user clicks on "accept customer" in the action-column.
             *
             * @param { Ext,data.Model } record - A single selected model
             */
            'acceptSingleCustomer',

            /**
             * Event fired when the user clicks on "decline customer" in the action-column.
             *
             * @param { Ext,data.Model } record - A single selected model
             */
            'declineSingleCustomer',

            /**
             * @param { Shopware.apps.SwagBusinessEssentials.view.list.BusinessEssentials } me - The view-instance
             * @param { Object } button - The "open customer"-object, which will be a button in the action-column
             */
            me.eventAlias + '-openCustomer-button-created',

            /**
             * @param { Shopware.apps.SwagBusinessEssentials.view.list.BusinessEssentials } me - The view-instance
             * @param { Object } button - The "accept customer"-object, which will be a button in the action-column
             */
            me.eventAlias + '-acceptCustomer-button-created',

            /**
             * @param { Shopware.apps.SwagBusinessEssentials.view.list.BusinessEssentials } me - The view-instance
             * @param { Object } button - The "decline customer"-object, which will be a button in the action-column
             */
            me.eventAlias + '-declineCustomer-button-created'
        );
    },

    /**
     * Overwrites the default "add"-button.
     * The "add"-button does not add new entries in this case. It reacts on selected records,
     * so the button has to be disabled initially.
     *
     * @returns { Ext.button.Button }
     */
    createAddButton: function() {
        var me = this,
            addButton = me.callParent();

        addButton.setDisabled(true);

        addButton.setHandler(function() {
            me.fireEvent('acceptCustomers', me.selModel.getSelection());
        });

        return addButton;
    },

    /**
     * Overwrites the default delete-button function.
     * In this case the selected records shall not be deleted, a custom functionality is implemented here.
     *
     * @returns { Ext.button.Button }
     */
    createDeleteButton: function() {
        var me = this,
            deleteButton = me.callParent();

        deleteButton.setHandler(function() {
            me.fireEvent('declineCustomers', me.selModel.getSelection());
        });

        return deleteButton;
    },

    /**
     * Overwrites the default action-column.
     * Adds another icon to open the related customer information in the customer-module.
     * Additionally the "Edit" and the "Delete"-actions are unnecessary and have to be replaced.
     *
     * @returns { Array }
     */
    createActionColumnItems: function() {
        var me = this,
            items = [];

        items.push(me.createCustomerButton());
        items.push(me.createAcceptButton());
        items.push(me.createDeclineButton());

        return items;
    },

    /**
     * Creates the button-object to open a customer in the customer-module.
     *
     * @returns { Object }
     */
    createCustomerButton: function() {
        var me = this,
            button;

        button = {
            action: 'openCustomer',
            iconCls: 'sprite-user',
            handler: function(view, rowIndex, colIndex, item, opts, record) {
                /** Open the customer */
                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.Customer',
                    action: 'detail',
                    params: {
                        customerId: ~~(1 * record.get('id'))
                    }
                });
            }
        };

        me.fireEvent(me.eventAlias + '-openCustomer-button-created', me, button);

        return button;
    },

    /**
     * Creates the "accept entry"-button.
     *
     * @returns { Object }
     */
    createAcceptButton: function() {
        var me = this,
            button;

        button = {
            action: 'acceptCustomer',
            iconCls: 'sprite-plus-circle-frame',
            handler: function(view, rowIndex, colIndex, item, opts, record) {
                me.fireEvent('acceptSingleCustomer', record);
            }
        };

        me.fireEvent(me.eventAlias + '-acceptCustomer-button-created', me, button);

        return button;
    },

    /**
     * Creates the "decline entry"-button.
     *
     * @returns { Object }
     */
    createDeclineButton: function() {
        var me = this,
            button;

        button = {
            action: 'declineCustomer',
            iconCls: 'sprite-minus-circle-frame',
            handler: function(view, rowIndex, colIndex, item, opts, record) {
                me.fireEvent('declineSingleCustomer', record);
            }
        };

        me.fireEvent(me.eventAlias + '-declineCustomer-button-created', me, button);

        return button;
    },

    /**
     * Add name to the default paging-bar.
     *
     * @returns { Ext.toolbar.Paging }
     */
    createPagingbar: function() {
        var me = this,
            pagingBar = me.callParent(arguments);

        pagingBar.itemId = 'business-essentials-request-pagingbar';

        return pagingBar;
    }
});
// {/block}
