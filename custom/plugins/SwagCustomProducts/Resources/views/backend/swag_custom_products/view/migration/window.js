//

//{namespace name="backend/swag_custom_products/migration/window"}
//{block name="backend/swag_custom_products/migration/window"}
Ext.define('Shopware.apps.SwagCustomProducts.view.migration.Window', {
    extend: 'Enlight.app.Window',

    width: 600,
    height: null,

    modal: true,

    layout: 'anchor',

    title: '{s name="window/title"}Custom Products - Migration{/s}',

    snippets: {
        columns: {
            name: '{s name="grid/column/name"}Name{/s}',
            description: '{s name="grid/column/description"}Description{/s}'
        },
        okButton: '{s name="progress/migration/okButton"}Close window{/s}',
        descriptionTitle: '{s name="window/description/title"}Migration information{/s}',
        description: '{s name="window/description/description"}You can import the groups already created by Custom Products in the new plugin. For this, the data \"migrated \". Depending on what you need to be adapted to create the data yet. <br /> <br /> If you have deposited prices, these must now be provided with a VAT rate (after migration only standart rate). Percentage rates no longer exist and are at fixed prices. This should also be adjusted. <br /> <br /> After adjusting the data to their Custom Products You have the groups / templates only activate and you can resell.{/s}',
        gridTitle: '{s name="window/grid/title"}Possible groups{/s}',
        startMigration: '{s name="window/grid/button/start"}Start migration now{/s}',
        doNotShowAgain: '{s name="window/checkbox/doNotShowAgain"}Do not show the migration button again.{/s}'
    },

    uri: {
        getGroupsUri: '{url controller="SwagCustomProducts" action="getOldGroupsForMigration"}',
        saveHideMigration: '{url controller="SwagCustomProducts" action="saveHideMigrationButton"}'
    },

    /**
     * init the component... Create items and docked items witch are necessary for building this window.
     */
    initComponent: function () {
        var me = this;

        me.items = me.createItems();
        me.dockedItems = me.createDockedItems();

        me.callParent(arguments);
    },

    /**
     * Create a array of different Ext.form.field.* types.. This are the
     * components that are displaying in the window.
     *
     * @returns { object[] }
     */
    createItems: function () {
        var me = this;

        return [
            me.createGroupGrid(),
            me.createStartMigrationContainer(),
            me.createDescription(),
            me.createCheckBox()
        ];
    },

    /**
     * Create a Ext.toolbar.Toolbar as toolbar for this window.
     * The toolbar is the primary container for the main actions like "close", "accept", "save"
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
     * Create a Ext.button.Button for handle this Window. The handler checks the form and
     * loads the ListingForm again.
     *
     * @returns { Ext.button.Button }
     */
    createOkButton: function () {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: me.snippets.okButton,
            cls: 'primary',
            handler: Ext.bind(me.onClickOkButton, me)
        });
    },

    /**
     * Return a Ext.grid.Panel for displaying groups that can be migrated.
     *
     * @returns { Ext.grid.Panel }
     */
    createGroupGrid: function () {
        var me = this;

        me.grid = Ext.create('Ext.grid.Panel', {
            anchor: '100%',
            height: 200,
            store: me.createGroupStore(),
            columns: me.createGridColumns(),
            selModel: me.createSelectionModel()
        });

        return me.grid;
    },

    /**
     * Return a Ext.selection.CheckboxModel. This is for selecting groups to migrate them.
     * On select is a check for the start migration button to en/disable them.
     *
     * @returns { Ext.selection.CheckboxModel }
     */
    createSelectionModel: function () {
        var me = this;

        return Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: Ext.bind(me.onSelectionChange, me)
            }
        });
    },

    /**
     * Returns a Ext.form.FieldSet with the description for the migration tool.
     *
     * @returns { Ext.form.FieldSet }
     */
    createDescription: function () {
        var me = this;

        return Ext.create('Ext.form.FieldSet', {
            margin: 10,
            title: me.snippets.descriptionTitle,
            html: me.snippets.description
        });
    },

    /**
     * Returns a Ext.form.FieldSet with a checkbox to hide the migration button.
     *
     * @returns { Ext.form.FieldSet }
     */
    createCheckBox: function () {
        var me = this;

        me.checkBox = Ext.create('Ext.form.field.Checkbox', {
            labelWidth: '100%',
            fieldLabel: me.snippets.doNotShowAgain,
            inputValue: true
        });

        return Ext.create('Ext.form.FieldSet', {
            margin: 10,
            items: [me.checkBox]
        });
    },

    /**
     * Return a array with the columns for me.grid. This is for displaying the group name and description. So the
     * user has a choice what groups he wants to migrate.
     *
     * @returns { object[] }
     */
    createGridColumns: function () {
        var me = this;

        return [
            {
                text: me.snippets.columns.name,
                dataIndex: 'name',
                flex: 1
            }, {
                text: me.snippets.columns.description,
                dataIndex: 'description',
                flex: 3
            }
        ];
    },

    /**
     * Return a Ext.data.Store with all groups that the user can migrate.
     *
     * @returns { Ext.data.Store }
     */
    createGroupStore: function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            fields: ['id', 'name', 'description'],
            proxy: {
                type: 'ajax',
                api: {
                    read: me.uri.getGroupsUri
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        }).load();
    },

    /**
     * Returns a Ext.container.Container with a button for starting the migration process.
     *
     * @returns { Ext.container.Container }
     */
    createStartMigrationContainer: function () {
        var me = this;

        return Ext.create('Ext.container.Container', {
            width: '100%',
            padding: '20 10 10 10',
            style: {
                textAlign: 'center'
            },
            items: [
                me.createStartMigrationButton()
            ]
        });
    },

    /**
     * Returns a Ext.button.Button for starting the migration process. If the user has selectes a one or more groups,
     * the button is enabled and the user can start the migration process.
     *
     * @returns { Ext.button.Button }
     */
    createStartMigrationButton: function () {
        var me = this;

        me.startMigrationButton = Ext.create('Ext.button.Button', {
            cls: 'primary',
            style: {
                margin: '0 auto'
            },
            text: me.snippets.startMigration,
            disabled: true,
            handler: Ext.bind(me.onStartMigration, me)
        });

        return me.startMigrationButton;
    },

    /**
     * This is the handler for the startMigrationButton.
     * On click we open the progress window and start the migration process.
     */
    onStartMigration: function () {
        var me = this,
            selection = me.grid.getSelectionModel().getSelection();

        me.processWindow = Ext.create('Shopware.apps.SwagCustomProducts.view.migration.Process', {
            selection: selection
        });

        me.processWindow.show();
    },

    /**
     * Is the Ext.selection.Model change this handler check for the length of the selection. If the selection
     * greater than "0" the startMigrationButton is set to enable. Else the button is set to disable.
     * This procedure is only for the usability. So the user can see onSelect, what is the next step.
     *
     * @param { Ext.selection.Model } model
     * @param { Ext.data.Model[] } selection
     */
    onSelectionChange: function (model, selection) {
        var me = this;

        if (selection.length == 0) {
            me.startMigrationButton.disable();
            return;
        }

        me.startMigrationButton.enable();
    },

    /**
     * On click "OK", refresh the template listing and close this window.
     * If the hideMigrationButton checkbox is checked we send a request for safe the setting. So the
     * migrationButton is hide.
     */
    onClickOkButton: function () {
        var me = this,
            doNotShowAgain = me.checkBox.getValue();

        me.listing.getStore().load();

        if (!doNotShowAgain) {
            me.close();
            return;
        }

        Ext.Ajax.request({
            url: me.uri.saveHideMigration,
            params: { doNotShowAgain: doNotShowAgain },
            success: function () {
                me.listing.migrationButton.hide();
            },
            failure: function (response) {
                Shopware.Notification.createGrowlMessage('Error', response);
            }
        });

        me.close();
    }
});
//{/block}