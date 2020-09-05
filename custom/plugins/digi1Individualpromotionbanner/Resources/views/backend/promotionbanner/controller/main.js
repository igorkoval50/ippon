Ext.define('Shopware.apps.Promotionbanner.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;

        me.control({
            'promotionbanner-listing-grid': {
                'copy-promotionbanner': me.displayPromotionbannerProcessWindow
            }
        });
        
        Shopware.app.Application.on('copy-promotionbanner-process', me.onCopyPromotionbanner);

        me.mainWindow = me.getView('list.Window').create({ }).show();
    },
    
    displayPromotionbannerProcessWindow: function(grid) {
        var selection = grid.getSelectionModel().getSelection(),
            store = grid.getStore();

        if (selection.length <= 0) return;

        Ext.create('Shopware.window.Progress', {
            title: '{s name=PromotionbannerListBatchProcessing}Promotionbanner kopieren{/s}',
            configure: function() {
                return {
                    tasks: [{
                        event: 'copy-promotionbanner-process',
                        data: selection,
                        text: '{s name=PromotionbannerListBatchProcessingText}Eintrag [0] von [1]{/s}'
                    }],

                    infoText: '{s name=PromotionbannerListBatchProcessingInfoTextHeadline}<h2>Die ausgewählten Promotionbanner werden kopiert.</h2>{/s}' +
                        '{s name=PromotionbannerListBatchProcessingInfoText1}Um den Prozess abzubrechen, kannst du den `<b><i>Abbrechen</i></b>` Button verwenden.{/s} ' +
                        '{s name=PromotionbannerListBatchProcessingInfoText2}Abhängig von der Datenmenge kann dieser Prozess einige Minuten in Anspruch nehmen.{/s}'
                }
            }
        }).show();
    },
    
    onCopyPromotionbanner: function (task, record, callback) {
        Ext.Ajax.request({
            url: '{url controller=Promotionbanner action=copyPromotionbanner}',
            method: 'POST',
            params: {
                promotionbannerId: record.get('id')
            },
            success: function(response, operation) {
                callback(response, operation);
            }
        });

        if(record.store){
            record.store.load();
        }
    }
});