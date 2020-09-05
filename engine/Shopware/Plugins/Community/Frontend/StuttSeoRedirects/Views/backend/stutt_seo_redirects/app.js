
Ext.define('Shopware.apps.StuttSeoRedirects', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.StuttSeoRedirects',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.Redirect',
        'list.Import',
        'list.Export',

        'detail.Redirect',
        'detail.Window'
    ],

    models: [ 'Redirect' ],
    stores: [ 'Redirect' ],


    //remove listeners
    globalEvents: [
        'import-csv',
        'export-csv',
        'reload-local-listing'
    ],

    dynamicEvents: [
    ],

    windowClasses: [
        'Shopware.apps.StuttSeoRedirects.view.list.Import',
        'Shopware.apps.StuttSeoRedirects.view.list.Export',
        'Shopware.apps.StuttSeoRedirects.view.list.Redirect',
        'Shopware.apps.StuttSeoRedirects.view.list.Window',
        'Shopware.apps.StuttSeoRedirects.view.detail.Redirect',
        'Shopware.apps.StuttSeoRedirects.view.detail.Window'
    ],

    launch: function() {
        return this.getController('Main').mainWindow;
    },

    onBeforeLaunch: function() {
        var me = this;

        me._destroyGlobalListeners(function() {
            me._destroyOtherModuleInstances(function() {
            });
        });

        me.callParent(arguments);
    },
    _destroyGlobalListeners: function(callback) {
        var me = this;
        var events = Shopware.app.Application.events;

        for (var key in events) {
            var event = events[key];

            if (me.globalEvents.indexOf(event.name) >= 0 && event.listeners.length > 0) {
                Ext.each(event.listeners, function(listener) {
                    if(!listener) {
                        return;
                    }

                    Shopware.app.Application.removeListener(
                        event.name,
                        listener.fn,
                        listener.scope
                    );
                });
            }

            Ext.each(me.dynamicEvents, function(eventName) {
                if (event.name && event.name.indexOf(eventName) >= 0) {
                    Ext.each(event.listeners, function(listener) {
                        if (listener) {
                            Shopware.app.Application.removeListener(
                                event.name,
                                listener.fn,
                                listener.scope
                            );
                        }
                    });
                }
            });
        }

        callback();
    },

    _destroyOtherModuleInstances: function (cb, cbArgs) {
        var me = this, activeWindows = [], subAppId = me.$subAppId;
        cbArgs = cbArgs || [];

        Ext.each(Shopware.app.Application.subApplications.items, function (subApp) {

            if (!subApp || !subApp.windowManager || subApp.$subAppId === subAppId || !subApp.windowManager.hasOwnProperty('zIndexStack')) {
                return;
            }
            Ext.each(subApp.windowManager.zIndexStack, function (item) {
                if (typeof(item) !== 'undefined' && me.windowClasses.indexOf(item.$className) > -1) {
                    activeWindows.push(item);
                }
            });
        });

        if (activeWindows && activeWindows.length) {
            Ext.each(activeWindows, function (win) {
                win.destroy();
            });

            if (Ext.isFunction(cb)) {
                cb.apply(me, cbArgs);
            }
        } else {
            if (Ext.isFunction(cb)) {
                cb.apply(me, cbArgs);
            }
        }
    }
});