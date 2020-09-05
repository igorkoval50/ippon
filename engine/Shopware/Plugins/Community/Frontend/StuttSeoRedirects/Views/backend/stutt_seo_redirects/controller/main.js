//{namespace name=backend/stutt_seo_redirects/controller/main}

Ext.define('Shopware.apps.StuttSeoRedirects.controller.Main', {
    extend: 'Enlight.app.Controller',


    refs: [
        { ref: 'localListing', selector: 'stutt-seo-redirects-listing-grid' }
    ],

    init: function() {
        var me = this;

        Shopware.app.Application.on(me.getEventListeners());

        me.control({
            'stutt-seo-redirects-listing-grid': {
                'change-active-state': me.displayProcessWindow,
                'open-csv-import': me.displayImportWindow,
                'open-csv-export': me.displayExportWindow
            }
        });

        me.mainWindow = me.getView('list.Window').create({ }).show();
    },

    getEventListeners: function() {
        var me = this;

        return {
            'change-active-state-process':  me.onChangeActiveStateProcess,
            'import-csv':                   me.importCsv,
            'export-csv':                   me.exportCsv,
            'reload-local-listing':         me.reloadLocalListing,
            scope: me
        };
    },

    importCsv: function(form, callback) {
        var me = this;

        form.submit({
            onSuccess: function(response) {
                var result = Ext.decode(response.responseText);
                if (!result) {
                    result = Ext.decode(response.responseXML.body.childNodes[0].innerHTML);
                }

                if (result.success) {
                    Shopware.Notification.createGrowlMessage('SEO-Weiterleitungen', '{s name="csv_import_finished"}CSV-Import abgeschlossen{/s}');
                    if (Ext.isFunction(callback)) {
                        callback();
                    }
                } else {
                    Shopware.Notification.createStickyGrowlMessage({
                        title: 'Error',
                        text: result.message,
                        width: 350
                    });
                }
            }
        });
    },

    exportCsv: function(form, callback) {
        var me = this;

        form.submit({
            onSuccess: function(response) {
                var result = Ext.decode(response.responseText);
                if (!result) {
                    result = Ext.decode(response.responseXML.body.childNodes[0].innerHTML);
                }

                if (result.success) {
                    Shopware.Notification.createGrowlMessage('SEO-Weiterleitungen', '{s name="csv_export_finished"}Export abgeschlossen{/s}');
                    var blob = new Blob([result.data], { type: 'text/csv' }),
                        e    = document.createEvent('MouseEvents'),
                        a    = document.createElement('a');

                    a.download = 'seo-redirects-export.csv';
                    a.href = window.URL.createObjectURL(blob);
                    a.dataset.downloadurl =  ['text/csv', a.download, a.href].join(':');
                    e.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                    a.dispatchEvent(e);

                    if (Ext.isFunction(callback)) {
                        callback();
                    }
                } else {
                    Shopware.Notification.createStickyGrowlMessage({
                        title: 'Error',
                        text: result.message,
                        width: 350
                    });
                }
            }
        });
    },

    reloadLocalListing: function() {
        var me = this,
            localListing = me.getLocalListing();

        localListing.getStore().load();
    },

    displayImportWindow: function() {
        var me = this;

        me.getView('list.Import').create().show();
    },

    displayExportWindow: function() {
        var me = this;

        me.getView('list.Export').create().show();
    },

    onChangeActiveStateProcess: function (task, record, callback) {
        Ext.Ajax.request({
            url: '{url controller=StuttSeoRedirects action=changeActiveState}',
            method: 'POST',
            params: {
                productId: record.get('id')
            },
            success: function(response, operation) {
                callback(response, operation);
            }
        });
    },

    displayProcessWindow: function(grid) {
        var selection = grid.getSelectionModel().getSelection();

        if (selection.length <= 0) return;

        Ext.create('Shopware.window.Progress', {
            title: '{s name="activate_deactivate_redirects"}Weiterleitungen aktivieren / deaktivieren{/s}',
            configure: function() {
                return {
                    tasks: [{
                        event: 'change-active-state-process',
                        data: selection,
                        text: '{s name="redirect_x_of_y"}Weiterleitung [0] von [1]{/s}'
                    }],

                    infoText: '<h2>{s name="activate_deactivate_redirects"}Weiterleitungen aktivieren / deaktivieren{/s}</h2>' +
                    '{s name="activate_deactivate_redirects1"}Sie können den Vorgang mit dem Button{/s} <b><i>`{s name="activate_deactivate_redirects2"}Prozess beenden{/s}`</i></b> {s name="activate_deactivate_redirects3"}jederzeit abbrechen{/s}. ' +
                    '{s name="activate_deactivate_redirects4"}Je nach dem, wie viele Weiterleitungen Sie ausgewählt haben, könnte es eine Weile dauern. Wir warten...{/s}'
                }
            }
        }).show();
    }
});