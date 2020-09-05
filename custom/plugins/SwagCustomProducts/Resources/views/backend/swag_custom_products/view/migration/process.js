//

//{namespace name="backend/swag_custom_products/migration/window"}
//{block name="backend/swag_custom_products/migration/process"}
Ext.define('Shopware.apps.SwagCustomProducts.view.migration.Process', {
    extend: 'Enlight.app.Window',

    width: 500,
    height: null,

    modal: true,

    layout: 'anchor',

    title: '{s name="window/title"}Custom Products - Migration{/s}',

    snippets: {
        description: '{s name="progress/migration/description"}{/s}',
        migrationState: '{s name="progress/migration/state"}Migration progress{/s}',
        okButton: '{s name="progress/migration/okButton"}Ready{/s}',
        columns: {
            name: '{s name="grid/column/name"}Name{/s}',
            success: '{s name="grid/column/success"}Success{/s}'
        }
    },

    uri: {
        startMigrationUri: '{url controller="SwagCustomProducts" action="migrate"}'
    },

    /**
     * Init the process window. On init we create a queue to handle the selected groups.
     * All selected groups are add to the queue. After we create the necessary items for this window.
     */
    initComponent: function () {
        var me = this;

        me.queue = Ext.create('Shopware.apps.SwagCustomProducts.view.components.Queue');
        me.queue.enQueue(me.selection);

        me.items = me.createItems();
        me.dockedItems = me.createDockedItems();

        me.callParent(arguments);
    },

    /**
     * afterShow this window we start the migration process.
     * For this we call the method. migrate();
     *
     * @overwrite
     */
    afterShow: function () {
        var me = this;

        me.callParent(arguments);

        me.migrate();
    },

    /**
     * This is the migrate function. At first we check if the a response because the initial call has no
     * response and no scope. The following calls come from the method itself by calling the method callAjax.
     * Also this method update´s the progressBar and add the migrated item to the gridStore, DeQueue´s a new item for
     * the next migration process.
     *
     * @param { object= } response
     * @param { object= } scope
     */
    migrate: function (response, scope) {
        var me = scope || this,
            response = response || null;

        if (response) {
            if (response.success && me.currentItem) {
                me.currentItem.data.success = true;
                me.grid.getStore().add(me.currentItem);

                Ext.defer(me.refreshGridView, 40, me);

                me.updateProgress(scope);
            }

            if (!response.success && me.currentItem) {
                me.currentItem.data.success = false;
                me.grid.getStore().add(me.currentItem);
                me.updateProgress(scope);
            }

            me.currentItem.set('errorLog', response.errorLog);
            me.currentItem.set('errorCount', response.errorCount);
        }

        me.currentItem = me.queue.deQueue();

        if (!me.currentItem) {
            me.oKButton.enable();
            me.updateProgress();
            return;
        }

        me.callAjax(
            me.uri.startMigrationUri,
            { id: me.currentItem.get('id') },
            me.migrate,
            me,
            me.updateProgress
        );
    },

    /**
     * Refresh the gridView
     */
    refreshGridView: function () {
        this.grid.getView().refresh();
    },

    /**
     * Update the progressbar. If the call came from a AjaxRequest we need a scope to accomplish the items in this
     * window. For update the progressBar we get some information like "step", "length" from the queue.
     *
     * @param { object= } scope
     */
    updateProgress: function (scope) {
        var me = scope || this;

        me.progressBar.updateProgress(
            me.queue.getPercentage(),
            me.queue.getCurrentStep() + ' / ' + me.queue.getStartLength(),
            true
        );
    },

    /**
     * Create the necessary items for this window and return a array of object´s.
     *
     * @returns { object[] }
     */
    createItems: function () {
        var me = this;

        return [
            me.createContainer(),
            me.createGridFieldSet()
        ];
    },

    /**
     * Create a Ext.toolbar.Toolbar for the mainActions in this window like "Close window"
     * This toolbar contains a button to for close this window.
     *
     * @returns { Ext.toolbar.Toolbar }
     */
    createDockedItems: function () {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            ui: 'shopware-ui',
            dock: 'bottom',
            cls: 'shopware-toolbar',
            items: [
                '->',
                me.createOkButton()
            ]
        });
    },

    /**
     * Create a button for the toolbar. This button handles the closeFunction of this window.
     *
     * @returns { Ext.button.Button }
     */
    createOkButton: function () {
        var me = this;

        me.oKButton = Ext.create('Ext.button.Button', {
            text: me.snippets.okButton,
            cls: 'primary',
            disabled: true,
            handler: Ext.bind(me.close, me)
        });

        return me.oKButton;
    },

    /**
     * Create and return a Ext.container.Container with the description and progressbar container.
     *
     * @returns { Ext.container.Container }
     */
    createContainer: function () {
        var me = this;

        return Ext.create('Ext.container.Container', {
            margin: 20,
            items: [
                me.createDescriptionContainer(),
                me.createProgressBar()
            ]
        })
    },

    /**
     * Create and return a Ext.container.Container with the description / information for this window.
     *
     * @returns { Ext.container.Container }
     */
    createDescriptionContainer: function () {
        var me = this;

        return Ext.create('Ext.container.Container', {
            html: me.snippets.description
        });
    },

    /**
     * Create and return a Ext.progessbar for displaying the current progress. Each selected group is a step of the bar.
     * If a group successful migrated or a error is occurred, we update the progress.
     *
     * @returns { Ext.ProgressBar }
     */
    createProgressBar: function () {
        var me = this;

        me.progressBar = Ext.create('Ext.ProgressBar', {
            text: '0 / ' + me.queue.getStartLength(),
            margin: '20 0 10 0',
            anchor: '100%'
        });

        return me.progressBar;
    },

    /**
     * Create a Ext.form.FieldSet with the grid for displaying the groupProgress.
     *
     * @returns { Ext.form.FieldSet }
     */
    createGridFieldSet: function () {
        var me = this;

        return Ext.create('Ext.form.FieldSet', {
            margin: 20,
            title: me.snippets.migrationState,
            items: [
                me.createGrid()
            ]
        });
    },

    /**
     * This is the grid with the groupMigrationProgress. Each group which has undergone the migration process represents
     * a row in this grid. The grid contains the name of the group, the count of occurred error and a actionButton for
     * displaying the errorDetails.
     *
     * @returns { Ext.grid.Panel }
     */
    createGrid: function () {
        var me = this;

        me.grid = Ext.create('Ext.grid.Panel', {
            height: 140,
            store: me.createDoneMigrationsStore(),
            columns: me.createColumns()
        });

        return me.grid;
    },

    /**
     * Create the columns of the grid. Each row contains a number for the current row, a success indicator which
     * indicates the success of the migration process, the name of the migrated group, the count of occurred errors,
     * and a actionColumn for displaying the occurred errors in a new window.
     *
     * @returns { object[] }
     */
    createColumns: function () {
        var me = this,
            actionColumnItems = me.createActionColumnItems();

        return [
            {
                xtype: 'rownumberer'
            }, {
                text: me.snippets.columns.success,
                dataIndex: 'success',
                width: 65,
                renderer: me.booleanColumnRenderer
            }, {
                text: me.snippets.columns.name,
                dataIndex: 'name',
                flex: 1
            }, {
                dataIndex: 'errorCount',
                width: 20
            }, {
                xtype: 'actioncolumn',
                width: actionColumnItems.length * 30,
                items: actionColumnItems,
                hideable: false,
                sortable: false,
                menuDisabled: true
            }
        ];
    },

    /**
     * Create the actionColumn with the button for displayin the occured errors. If the errorCount = 0 we hide the
     * actionColumn because there is nothing to see.
     *
     * @returns { object[] }
     */
    createActionColumnItems: function () {
        var me = this;

        return [{
            iconCls: 'sprite-exclamation-red',
            handler: Ext.bind(me.showErrorsClick, me),
            getClass: function (obj, metadata, record) {
                var errorCount = record.get('errorCount');

                if (errorCount === 0) {
                    return 'x-hidden';
                }
            }
        }];
    },

    /**
     * Show the error log.
     * We call the Ext.msg and get the errorLog from the recod. This errorLog is a array. This array we join to
     * a string we can display. Each error in the log get a trailing ErrorNumber.
     *
     * @param { Ext.grid.Panel } grid
     * @param { * } row
     * @param { * } col
     * @param { Ext.Button } button
     * @param { string } type
     * @param { Ext.data.Model } record
     */
    showErrorsClick: function (grid, row, col, button, type, record) {
        var logEntries = record.get('errorLog'),
            log = [];

        Ext.Array.each(logEntries, function (logEntry, index) {
            log[index] = [
                (index + 1),
                '. ',
                logEntry,
                '<br />'
            ].join('');
        });

        Ext.Msg.show({
            title: 'Error log',
            msg: log.join(''),
            buttons: Ext.Msg.OK,
            icon: Ext.Msg.ERROR
        });
    },

    /**
     * Create an return a simple Ext.data.Store to handle the groups which has undergone the migration process.
     *
     * @returns { Ext.data.Store }
     */
    createDoneMigrationsStore: function () {
        return Ext.create('Ext.data.Store', {
            fields: ['id', 'name', 'success', 'errorLog', 'errorCount']
        });
    },

    /**
     * This is the columnRenderer for the success column. This renderer return a tick or a redCross in HTML,
     * for indicate the success or fail of the migration process.
     *
     * @param { bool } value
     * @returns { string }
     */
    booleanColumnRenderer: function (value) {
        var checked = 'sprite-ui-check-box-uncheck';

        if (value == true) {
            checked = 'sprite-ui-check-box';
        }

        return '<span style="text-align:center; display:block; height:15px; width:15px; margin: 0 auto;" class="' + checked + '"></span>';
    },

    /**
     * This is the callAjax method. This method is calling a AjaxRequest to the server. For this procedure
     * u need a uri that u want to call, optional parameter, a callBackFunction, the scope, and a onError function
     * to handle a failure. If there are any failure, the method trigger a growlMessage with the serverResponse.
     *
     * @param { string } url
     * @param { object } parameter
     * @param { function } callback
     * @param { object } scope
     * @param { function } onError
     */
    callAjax: function (url, parameter, callback, scope, onError) {
        Ext.Ajax.request({
            url: url,
            params: parameter,
            success: function (response) {
                var responseData = Ext.JSON.decode(response.responseText);

                callback(responseData, scope);
            },
            failure: function () {
                onError(scope);
                Shopware.Notification.createGrowlMessage('Error', response);
            }
        });
    }
});
//{/block}