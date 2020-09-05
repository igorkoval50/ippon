//

// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/index/view/widgets/merchant"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.Index.swag_business_essentials.view.widgets.Merchant', {
    override: 'Shopware.apps.Index.view.widgets.Merchant',

    alias: 'widget.business_essentials-merchant-widget',

    customSnippets: {
        mainTitle: '{s name="MainTitle"}{/s}',
        growlMessage: {
            requestAcceptTitle: '{s name="RequestAcceptTitle"}{/s}',
            requestAcceptMessage: '{s name="RequestAcceptMessage"}{/s}',
            requestDeclineTitle: '{s name="RequestDeclineTitle"}{/s}',
            requestDeclineMessage: '{s name="RequestDeclineMessage"}{/s}',
            failureTitle: '{s name="MerchantWidgetFailureTitle"}{/s}',
            requestErrorMessage: '{s name="RequestErrorOccurred"}{/s}'
        }
    },

    urls: {
        acceptRequest: '{url controller=SwagBusinessEssentials action=acceptRequest}',
        declineRequest: '{url controller=SwagBusinessEssentials action=declineRequest}'
    },

    /**
     * Overwrites the default "createColumns"-method to change the button-handlers.
     */
    createColumns: function() {
        var me = this,
            columns = me.callParent(arguments),
            actionCol = columns[columns.length - 1],
            acceptBtn = actionCol.items[1],
            declineBtn = actionCol.items[2];

        acceptBtn.handler = Ext.bind(me.sendAcceptRequest, me);
        declineBtn.handler = Ext.bind(me.sendDeclineRequest, me);

        return columns;
    },

    /**
     * Shows a failure growl message.
     */
    showFailure: function() {
        var me = this;

        Shopware.Notification.createGrowlMessage(
            me.customSnippets.growlMessage.failureTitle,
            me.customSnippets.mainTitle
        );
    },

    /**
     * Sends the request to accept a customer.
     * Additionally shows a growl-message depending on the success of the request.
     *
     * @param { Ext.grid.View } gridView
     * @param { int } rowIndex
     * @param { int } colIndex
     * @param { Ext.button.Button } btn
     * @param { Event } event
     * @param { Ext.data.Model } record
     */
    sendAcceptRequest: function(gridView, rowIndex, colIndex, btn, event, record) {
        var me = this;

        Ext.Ajax.request({
            url: me.urls.acceptRequest,
            params: {
                id: record.get('id')
            },
            success: function(result) {
                var responseText = Ext.JSON.decode(result.responseText),
                    success = responseText.success,
                    acceptMessage = me.customSnippets.growlMessage.requestAcceptMessage,
                    errorMessage = me.customSnippets.growlMessage.requestErrorMessage;

                if (!success) {
                    acceptMessage += Ext.String.format(
                        '<br /><br />' +
                        '<b>[0]</b>' +
                        '<br />' + responseText.error,
                        errorMessage
                    );
                }

                Shopware.Notification.createGrowlMessage(
                    me.customSnippets.growlMessage.requestAcceptTitle,
                    acceptMessage,
                    me.customSnippets.mainTitle
                );

                gridView.getStore().load();
            },
            failure: function() {
                me.showFailure();
            }
        });
    },

    /**
     * Sends the request to decline a customer.
     * Additionally shows a growl-message depending on the success of the request.
     *
     * @param { Ext.grid.View } gridView
     * @param { int } rowIndex
     * @param { int } colIndex
     * @param { Ext.button.Button } btn
     * @param { Event } event
     * @param { Ext.data.Model } record
     */
    sendDeclineRequest: function(gridView, rowIndex, colIndex, btn, event, record) {
        var me = this;

        Ext.Ajax.request({
            url: me.urls.declineRequest,
            params: {
                id: record.get('id')
            },
            success: function(result) {
                var responseText = Ext.JSON.decode(result.responseText),
                    success = responseText.success,
                    denyMessage = me.customSnippets.growlMessage.requestDeclineMessage,
                    errorMessage = me.customSnippets.growlMessage.requestErrorMessage;

                if (!success) {
                    denyMessage += Ext.String.format(
                        '<br /><br />' +
                        '<b>[0]</b>' +
                        '<br />' + responseText.error,
                        errorMessage
                    );
                }

                Shopware.Notification.createGrowlMessage(
                    me.customSnippets.growlMessage.requestDeclineTitle,
                    denyMessage,
                    me.customSnippets.mainTitle
                );
                gridView.getStore().load();
            },
            failure: function() {
                me.showFailure();
            }
        });
    }
});
// {/block}
