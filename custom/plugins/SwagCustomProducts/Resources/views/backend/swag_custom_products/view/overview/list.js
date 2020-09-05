// {namespace name="backend/swag_custom_products/overview/list"}
// {block name="backend/swag_custom_products/overview/list"}
Ext.define('Shopware.apps.SwagCustomProducts.view.overview.List', {
    extend: 'Shopware.grid.Panel',

    alias: 'widget.swag-custom-products-overview-grid',

    duplicateUrl: '{url action="cloneTemplate"}',
    validateUrl: '{url action="validateInternalNameAjax"}',
    deleteUrl: '{url action="deleteTemplate"}',
    isMigrationPossibleUrl: '{url action="isMigrationPossible"}',
    updateUrl: '{url action="rowEditing"}',

    snippets: {
        duplicateInternalName: '{s name="header/internal_name/name_is_taken"}The name is already taken <br>{/s}',
        newInternalName: '{s name="header/internal_name/desc"}Please enter a new internal name.{/s}',
        migration: '{s name="button/migration"}Migration{/s}',
        grid: {
            internalName: '{s name="header/internal_name"}Internal name{/s}',
            displayName: '{s name="header/display_name"}Display name{/s}',
            products: '{s name="header/products"}Products{/s}',
            options: '{s name="header/options"}Options{/s}'
        },
        deleteMessageBox: {
            title: '{s name="msg/delete/title"}Delete{/s}',
            message: '{s name="msg/delete/message"}Do you really want to delete the Custom Product Template?{/s}'
        }
    },

    /**
     * on init check for installations of the old Customizing plugin. On failure we display a GrowlMessage.
     */
    initComponent: function () {
        var me = this;

        me.callAjax(
            me.isMigrationPossibleUrl,
            { },
            me.isMigrationPossibleCallback,
            me
        );

        me.callParent(arguments);
    },

    /**
     * @returns { Object }
     */
    configure: function () {
        var me = this;

        return {
            columns: me.getColumns(),
            rowEditing: true,
            detailWindow: 'Shopware.apps.SwagCustomProducts.view.detail.Window'
        };
    },

    /**
     * Overwrites the inherited method to enable row editing and registers the necessary event handler.
     *
     * @overwrite;
     * @inheritDoc
     */
    createPlugins: function () {
        var me = this,
            items = me.callParent(arguments);

        Ext.each(items, function (item) {
            if (item.$className == 'Ext.grid.plugin.RowEditing') {
                item.on('edit', Ext.bind(me.onInlineEdit, me));
            }
        });

        return items;
    },

    /**
     * Event listener method which will be when the user has edited a row using the inline editing.
     * Fires an AJAX request to save the modified record.
     *
     * @param { Shopware.apps.SwagCustomProducts.view.components.RowTextEditor } editor
     * @param { Object } e
     */
    onInlineEdit: function (editor, e) {
        var me = this;

        me.callAjax(
            me.updateUrl,
            e.record.data,
            me.rowEditingCallback,
            me
        );
    },

    /**
     * This is the callback action for the ajaxRequest rowEditing.
     * We check the response. If the response not successful we show
     * the errorMessage in a growlMessage.
     *
     * We reload the store in any case because on error we need to reset the record
     * and in case of success we need to update the record.
     *
     * @param response
     * @param scope
     */
    rowEditingCallback: function (response, scope) {
        var me = scope || this;

        response = Ext.JSON.decode(response.responseText);

        if (!response.success) {
            Shopware.Notification.createGrowlMessage('Error', response.errorMessage);
        }

        me.store.load();
    },

    /**
     * @returns { Object }
     */
    getColumns: function () {
        var me = this;

        return {
            internalName: { header: me.snippets.grid.internalName, editor: false },
            displayName: { header: me.snippets.grid.displayName, editor: me.getNameEditor() },
            productCount: { header: me.snippets.grid.products, sortable: false, editor: false },
            optionCount: { header: me.snippets.grid.options, sortable: false, editor: false }
        };
    },

    /**
     * This is the editor for the nameField. This contains the validator function in
     * the property validateValue.
     *
     * @returns { Shopware.apps.SwagCustomProducts.view.components.RowTextEditor }
     */
    getNameEditor: function () {
        return Ext.create('Shopware.apps.SwagCustomProducts.view.components.RowTextEditor');
    },

    /**
     * @overwrite
     * @returns { object[] }
     */
    createActionColumnItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(me.createDuplicateButton());

        return items;
    },

    /**
     * @overwrite
     * @return { Object }
     */
    createDeleteColumn: function () {
        var me = this,
            button = me.callParent(arguments);

        button.handler = Ext.bind(me.deleteButtonHandler, me);

        return button;
    },

    /**
     * Add the migration button to the toolbar. The button was show if a migration is possible.
     *
     * @overwrite
     */
    createToolbarItems: function () {
        var me = this,
            items = me.callParent(arguments),
            migrateButton = [ me.createMigrationButton() ];

        return migrateButton.concat(items);
    },

    /**
     * Create a Ext.button.Button for showing the migration window.
     *
     * @returns { Ext.button.Button }
     */
    createMigrationButton: function () {
        var me = this;

        me.migrationButton = Ext.create('Ext.button.Button', {
            iconCls: 'contents--import-export',
            hidden: true,
            text: me.snippets.migration,
            handler: Ext.bind(me.onMigrationButtonClick, me)
        });

        return me.migrationButton;
    },

    /**
     * Create and show the migration window
     */
    onMigrationButtonClick: function () {
        var me = this;

        me.migrationWindow = Ext.create('Shopware.apps.SwagCustomProducts.view.migration.Window', {
            listing: me
        });

        me.migrationWindow.show();
    },

    /**
     * @param { Ext.view.View } view
     * @param { int } rowIndex
     * @param { int } colIndex
     * @param { object } item
     * @param { object } opts
     * @param { Ext.data.Model | * } record
     */
    deleteButtonHandler: function (view, rowIndex, colIndex, item, opts, record) {
        var me = this;

        if (!record) {
            return;
        }

        Ext.Msg.show({
            title: me.snippets.deleteMessageBox.title,
            msg: me.snippets.deleteMessageBox.message,
            closable: false,
            buttons: Ext.Msg.YESNO,
            fn: function (btn) {
                if (btn == 'no') {
                    return;
                }

                me.callAjax(
                    me.deleteUrl,
                    { id: record.get('id') },
                    me.reloadStore,
                    me
                );
            }
        });
    },

    /**
     * @returns { object }
     */
    createDuplicateButton: function () {
        var me = this;

        return {
            iconCls: 'sprite-duplicate-article',
            handler: function (view, rowIndex, colIndex, item, opts, record) {
                me.copyRecord = record;
                me.getNewInternalName();
            }
        };
    },

    /**
     * @param showDuplicateMessage
     */
    getNewInternalName: function (showDuplicateMessage) {
        var me = this,
            message = me.snippets.newInternalName;

        if (showDuplicateMessage) {
            message = me.snippets.duplicateInternalName + me.snippets.newInternalName;
        }

        Ext.Msg.prompt(
            me.snippets.grid.internalName,
            message,
            me.checkInternalName,
            me,
            false,
            me.copyRecord.get('internalName')
        );
    },

    /**
     * @param { string } button
     * @param { string } internalName
     */
    checkInternalName: function (button, internalName) {
        var me = this;

        if (button == 'cancel') {
            return;
        }

        me.internalName = internalName;
        me.callAjax(
            me.validateUrl,
            { value: internalName },
            me.callDuplicateAction,
            me
        );
    },

    /**
     * @param { string } response
     * @param { object } scope
     */
    callDuplicateAction: function (response, scope) {
        var me = scope || this;

        response = Ext.JSON.decode(response.responseText);

        if (!response.success) {
            me.getNewInternalName(true);
            return;
        }

        me.callAjax(
            me.duplicateUrl,
            { id: me.copyRecord.get('id'), internalName: me.internalName },
            me.reloadStore,
            me
        );
    },

    /**
     * @param { string } url
     * @param { object } params
     * @param { function } callBack
     * @param { object } scope
     */
    callAjax: function (url, params, callBack, scope) {
        var me = scope || this;

        Ext.Ajax.request({
            url: url,
            params: params,
            success: function (response) {
                callBack(response, me);
            },
            failure: function () {
                Shopware.Notification.createGrowlMessage('Error');
            }
        });
    },

    /**
     * @param { string } response
     * @param { object } scope
     */
    reloadStore: function (response, scope) {
        var me = scope || this;

        me.store.reload();
    },

    /**
     * This is a callback for a ajaxCall. This handle the response and checks for the possibility if a migration
     * can executed. If a migration is possible we show the migrationButton.
     *
     * @param { object } response
     * @param { object } scope
     */
    isMigrationPossibleCallback: function (response, scope) {
        var me = scope || this;

        response = Ext.JSON.decode(response.responseText);

        if (response.migrationPossible) {
            me.migrationButton.show();
        }
    }
});
// {/block}
