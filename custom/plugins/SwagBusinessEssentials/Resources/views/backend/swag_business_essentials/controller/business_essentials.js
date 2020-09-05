// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/controller/business_essentials"}
Ext.define('Shopware.apps.SwagBusinessEssentials.controller.BusinessEssentials', {
    extend: 'Enlight.app.Controller',

    refs: [
        { ref: 'grid', selector: 'business_essentials-listing-grid' },
        { ref: 'pagingBar', selector: '#business-essentials-request-pagingbar' }
    ],

    snippets: {
        mainTitle: '{s name="MainTitle"}{/s}',
        progressCustomerText: '{s name="ProgressCustomerText"}{/s}',
        acceptInfoText: '{s name="AcceptCustomersInfoText"}{/s}',
        declineInfoText: '{s name="DeclineCustomersInfoText"}{/s}',
        growlMessage: {
            requestAcceptTitle: '{s name="RequestAcceptTitle"}{/s}',
            requestAcceptMessage: '{s name="RequestAcceptMessage"}{/s}',
            requestDeclineTitle: '{s name="RequestDeclineTitle"}{/s}',
            requestDeclineMessage: '{s name="RequestDeclineMessage"}{/s}',
            requestErrorMessage: '{s name="RequestErrorOccurred"}{/s}'
        }
    },

    init: function() {
        var me = this;

        me.control({
            'business_essentials-listing-grid': {
                acceptSingleCustomer: me.onAcceptSingleCustomer,
                declineSingleCustomer: me.onDeclineSingleCustomer,
                acceptCustomers: me.onAcceptCustomers,
                declineCustomers: me.onDeclineCustomers
            }
        });
    },

    /**
     * Opens the batch-processing to accept each customers request.
     *
     * @param { Ext.data.Model[] } records
     */
    onAcceptCustomers: function(records) {
        var me = this;

        Shopware.app.Application.on('accept-customer', function(task, record, callback) {
            me.acceptSingleCustomer(record, callback);
        });

        Ext.create('Shopware.window.Progress', {
            configure: function() {
                return {
                    tasks: [
                        {
                            event: 'accept-customer',
                            data: records,
                            text: me.snippets.progressCustomerText
                        }
                    ],
                    infoText: me.snippets.acceptInfoText
                };
            },
            listeners: {
                'destroy': function() {
                    me.reloadGrid();
                }
            }
        }).show();
    },

    /**
     * Opens the batch-processing to decline each customers request.
     *
     * @param { Ext.data.Model[] } records
     */
    onDeclineCustomers: function(records) {
        var me = this;

        Shopware.app.Application.on('decline-customer', function(task, record, callback) {
            me.declineSingleCustomer(record, callback);
        });

        Ext.create('Shopware.window.Progress', {
            configure: function() {
                return {
                    tasks: [
                        {
                            event: 'decline-customer',
                            data: records,
                            text: me.snippets.progressCustomerText
                        }
                    ],
                    infoText: me.snippets.declineInfoText
                };
            },
            listeners: {
                'destroy': function() {
                    me.reloadGrid();
                }
            }
        }).show();
    },

    /**
     * Accepts a single customer and then shows a growl-message and reloads the store.
     *
     * @param { Ext.data.Model } record
     */
    onAcceptSingleCustomer: function(record) {
        var me = this;

        me.acceptSingleCustomer(record, function(result) {
            var responseText = Ext.JSON.decode(result.responseText),
                success = responseText.success,
                acceptMessage = me.snippets.growlMessage.requestAcceptMessage,
                errorMessage = me.snippets.growlMessage.requestErrorMessage;

            if (!success) {
                acceptMessage += Ext.String.format(
                    '<br /><br />' +
                    '<b>[0]</b>' +
                    '<br />' + responseText.error,
                    errorMessage
                );
            }

            Shopware.Notification.createGrowlMessage(
                me.snippets.growlMessage.requestAcceptTitle,
                acceptMessage,
                me.snippets.mainTitle
            );

            me.reloadGrid();
        });
    },

    /**
     * Declines a single customer and then shows a growl-message and reloads the store.
     *
     * @param { Ext.data.Model } record
     */
    onDeclineSingleCustomer: function(record) {
        var me = this;

        me.declineSingleCustomer(record, function(result) {
            var responseText = Ext.JSON.decode(result.responseText),
                success = responseText.success,
                denyMessage = me.snippets.growlMessage.requestDeclineMessage,
                errorMessage = me.snippets.growlMessage.requestErrorMessage;

            if (!success) {
                denyMessage += Ext.String.format(
                    '<br /><br />' +
                    '<b>[0]</b>' +
                    '<br />' + responseText.error,
                    errorMessage
                );
            }

            Shopware.Notification.createGrowlMessage(
                me.snippets.growlMessage.requestDeclineTitle,
                denyMessage,
                me.snippets.mainTitle
            );

            me.reloadGrid();
        });
    },

    /**
     * Accepts a single customer. It will send an ajax-request to the controller to accept the customer.
     * This is also called from the batch-processing.
     *
     * @param { Ext.data.Model } record
     * @param { Function } callback
     */
    acceptSingleCustomer: function(record, callback) {
        callback = callback || Ext.emptyFn;

        Ext.Ajax.request({
            url: '{url controller=SwagBusinessEssentials action=acceptRequest}',
            params: {
                id: record.get('id')
            },
            success: function(result, operation) {
                callback(result, operation);
            }
        });
    },

    /**
     * Accepts a single customer. It will send an ajax-request to the controller to decline the customer.
     * This is also called from the batch-processing.
     *
     * @param { Ext.data.Model } record
     * @param { Function } callback
     */
    declineSingleCustomer: function(record, callback) {
        callback = callback || Ext.emptyFn;

        Ext.Ajax.request({
            url: '{url controller=SwagBusinessEssentials action=declineRequest}',
            params: {
                id: record.get('id')
            },
            success: function(result, operation) {
                callback(result, operation);
            }
        });
    },

    /**
     * Reloads the grid. If the current page is empty, because the last entry on this page was just removed,
     * it will move to the previous page.
     */
    reloadGrid: function() {
        var me = this;

        me.getGrid().getStore().load({
            callback: function(result) {
                if (result.length === 0) {
                    me.getPagingBar().movePrevious();
                }
            }
        });
    }
});
// {/block}
