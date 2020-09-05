/**
 * The window which will be created in the detail view of an option.
 */
// {namespace name="backend/swag_custom_products/option/window"}
// {block name="backend/swag_custom_products/view/option/window"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.Window', {
    alias: 'widget.swag-custom-products-option-window',
    extend: 'Shopware.window.Detail',

    // Preventing to open more than one detail window at the same time
    modal: true,
    minimizable: false,

    height: 600,
    width: 800,

    snippets: {
        title: {
            create: '{s name="title/create"}Create option{/s}',
            edit: '{s name="title/edit"}Edit option - [0]{/s}'
        },
        buttons: {
            save: '{s name="buttons/save"}Apply{/s}'
        },
        taxMessage: {
            title: '{s name="tax/invalid/message/title"}Empty tax rate{/s}',
            message: '{s name="tax/invalid/message/message"}Cannot save the prices without a tax. Please select a tax rate.{/s}'
        },
        values: {
            message: '{s name="values/invalid/message"}The selected component needs at least one value.{/s}'
        }
    },

    /** @type { Shopware.apps.SwagCustomProducts.model.Option } */
    record: null,

    /** @type { Shopware.apps.SwagCustomProducts.model.Template } */
    templateRecord: null,

    /**
     * Will be set to true by an input parameter if this window would create a new option record.
     *
     * @type boolean
     */
    isNewOption: false,

    /**
     * @overwrite
     */
    configure: function () {
        return {
            translationKey: 'customProductOptionTranslations'
        };
    },

    /**
     * @overwrite
     */
    initComponent: function () {
        var me = this;

        me.title = me.getTitle();

        me.callParent(arguments);

        me.copyRecord();
    },

    /**
     * @overwrite
     */
    onCancel: function () {
        var me = this;

        me.resetRecord();
        me.callParent(arguments);
    },

    /**
     * read the initial RecordData
     */
    copyRecord: function () {
        var me = this;

        me.recordManager = Ext.create('Shopware.apps.SwagCustomProducts.view.components.RecordCopyManager');
        me.recordCopy = me.recordManager.copy(me.record);
    },

    /**
     * set the InitialRecordData to the record
     */
    resetRecord: function () {
        var me = this;

        me.recordManager.resetRecord(me.record, me.recordCopy);
    },

    /**
     * @returns { String }
     */
    getTitle: function () {
        var me = this;

        if (me.record.get('name')) {
            return Ext.String.format(me.snippets.title.edit, me.record.get('name'));
        }

        return me.snippets.title.create;
    },

    /**
     * @overwrite
     * @returns { Ext.button.Button }
     */
    createSaveButton: function () {
        var me = this,
            button = me.callParent(arguments);

        button.text = me.snippets.buttons.save;

        return button;
    },

    /**
     * Override to prevent the server side saving request adn
     * add the record to the optionStore
     *
     * @overwrite
     */
    onSave: function () {
        var me = this,
            valueStore = me.record.getValuesStore;

        if (me.record.get('type') !== 'fileupload' &&
            me.record.get('type') !== 'imageupload' &&
            me.record.get('couldContainValues') &&
            valueStore.data.items.length <= 0) {
            Shopware.Notification.createGrowlMessage(me.getTitle(), me.snippets.values.message);
            return;
        }

        if (!me.formPanel.getForm().isValid()) {
            return;
        }

        if (!me.isRecordInStore()) {
            me.templateRecord.getOptions().add(me.record);
        }

        me.destroy();
    },

    /**
     * @overwrite
     */
    doClose: function () {
        var me = this;

        me.resetRecord();
        me.callParent(arguments);
    },

    /**
     * @returns { boolean }
     */
    isRecordInStore: function () {
        var me = this,
            isInStore = false;

        me.templateRecord.getOptions().each(function (item) {
            if (item.internalId == me.record.internalId) {
                isInStore = true;
            }
        });

        return isInStore;
    }
});
// {/block}
