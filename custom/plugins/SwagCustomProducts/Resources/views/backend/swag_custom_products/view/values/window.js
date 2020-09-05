// {namespace name="backend/swag_custom_products/value/window"}
// {block name="backend/swag_custom_products/value/window"}
Ext.define('Shopware.apps.SwagCustomProducts.view.values.Window', {
    extend: 'Enlight.app.Window',

    layout: 'fit',
    width: 750,
    height: null,
    modal: true,
    bodyPadding: 20,
    bodyStyle: {
        background: '#EEEEF0',
        border: '1px solid #A4B5C0 !important'
    },

    snippets: {
        title: '{s name="windorw/title"}Edit value{/s}',
        textFieldLabel: '{s name="fields/name/label"}Name{/s}',
        valueFieldLabel: '{s name="fields/value/label"}Value{/s}',
        orderNumberFieldLabel: '{s name="fields/orderNumber/label"}Order number{/s}',
        orderNumberFieldError: '{s name="fields/orderNumber/error"}This order number is already in use.{/s}',
        isDefaultValueFieldLabel: '{s name="fields/isDefault/label"}Is default{/s}',
        surchargeFieldLabel: '{s name="fields/surcharge/label"}Surcharge{/s}',
        isOnceSurchargeFieldLabel: '{s name="fields/onceSurcharge/label"}Once surcharge{/s}',
        cancelButtonText: '{s name="bbar/cancel/text"}Cancel{/s}',
        okButtonText: '{s name="bbar/ok/text"}Ok{/s}',
        defaultValueMessage: '{s name="default/value/message"}You have already defined a standard option. Do you want to overwrite?{/s}',
        defaultValueMessageOverwrite: '{s name="default/value/message/overwrite"}Overwrite{/s}',
        seoTitleLabel: '{s name="fields/seo_title/label"}SEO title{/s}',
        taxMessage: {
            title: '{s namespace="backend/swag_custom_products/option/window" name="tax/invalid/message/title"}Empty tax rate{/s}',
            message: '{s namespace="backend/swag_custom_products/option/window" name="tax/invalid/message/message"}Cannot save the prices without a tax. Please select a tax rate.{/s}'
        }
    },

    useColorPicker: false,
    useImagePicker: false,

    initComponent: function () {
        var me = this;

        me.title = me.snippets.title;
        me.items = me.createFormField();
        me.dockedItems = me.createDockedItems();

        me.orderNumberIsValid = true;
        me.orderNumberValidateUrl = '{url controller="SwagCustomProducts" action="validateValueOrderNumberAjax"}';

        me.callParent(arguments);
        me.hideFields();
        me.loadRecord();
        me.copyRecord();
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
     * @returns { Ext.form.Panel }
     */
    createFormField: function () {
        var me = this;

        me.formPanel = Ext.create('Ext.form.Panel', {
            layout: 'anchor',
            items: me.createItems(),
            plugins: me.createTranslationPlugin(),
            flex: 1,
            border: false,
            bodyStyle: {
                background: '#EEEEF0'
            }
        });

        return me.formPanel;
    },

    /**
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = [];

        me.nameField = me.createTextField('name', me.snippets.textFieldLabel, false, true);
        me.nameField.on('change', Ext.bind(me.onFieldChange, me));
        items.push(me.nameField);

        me.valueField = me.createValueField('value', me.snippets.valueFieldLabel);
        items.push(me.valueField);

        me.orderNumberField = me.createOrderNumberField('ordernumber', me.snippets.orderNumberFieldLabel);
        items.push(me.orderNumberField);

        me.isDefaultField = me.createCheckBoxField('isDefaultValue', me.snippets.isDefaultValueFieldLabel);
        items.push(me.isDefaultField);

        me.surchargeTaxRateField = me.createSurchargeGrid();
        items.push(me.surchargeTaxRateField);

        me.isOnceSurchargeField = me.createCheckBoxField('isOnceSurcharge', me.snippets.isOnceSurchargeFieldLabel);
        items.push(me.isOnceSurchargeField);

        if (me.optionType && me.optionType === 'imageselect') {
            me.seoTitleField = me.createSeoTitleField();
            items.push(me.seoTitleField);
        }

        return items;
    },

    /**
     * @returns { * }
     */
    createTranslationPlugin: function () {
        var me = this;

        if (!me.record.get('id')) {
            return [];
        }

        return [
            {
                ptype: 'translation',
                pluginId: 'translation',
                translationType: 'customProductValueTranslations',
                translationMerge: false,
                translationKey: me.record.get('id')
            }
        ];
    },

    /**
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
                me.createCancelButton(),
                me.createOkButton()
            ]
        });
    },

    /**
     * hide the ValueField if it`s useless
     */
    hideFields: function () {
        var me = this;

        if (!me.useColorPicker && !me.useImagePicker) {
            me.valueField.allowBlank = true;
            me.valueField.hide();
        }
    },

    /**
     * Load the record.. set the values to the fields
     */
    loadRecord: function () {
        var me = this;

        me.record.fields.each(function (field) {
            var fieldName = field.name,
                formFieldSelector = ['[name=', fieldName, ']'].join(''),
                formField = me.down(formFieldSelector);

            if (!formField) {
                return;
            }

            if (me.useImagePicker && fieldName == 'value') {
                me.valueField.setValue(me.record.get('mediaId'));
                return;
            }

            formField.setValue(me.record.get(fieldName));
        });
    },

    /**
     * @param { * } field
     * @param { mixed } newValue
     */
    onFieldChange: function (field, newValue) {
        var me = this,
            showDefaultValueMessage = false;

        if (field.getName() == 'isDefaultValue' && newValue) {
            me.valueGrid.getStore().each(function (valueObject) {
                if (valueObject.get('isDefaultValue') && valueObject.internalId != me.record.internalId) {
                    showDefaultValueMessage = true;
                    me.oldDefaultValue = valueObject;
                    me.showDefaultValueChangeMessage();
                    return;
                }
                valueObject.set(field.getName(), false);
            });
        }

        if (showDefaultValueMessage) {
            return;
        }

        if (me.useImagePicker && field.getName() == 'value') {
            me.record.set('mediaId', newValue.get('id'));
            me.record.set(field.getName(), newValue.get('thumbnail'));
            return;
        }

        me.record.set(field.getName(), newValue);
    },

    /**
     * Open a MessageBox. Ask for overwrite the default value
     */
    showDefaultValueChangeMessage: function () {
        var me = this;

        Ext.Msg.show({
            title: me.snippets.defaultValueMessageOverwrite,
            msg: me.snippets.defaultValueMessage,
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            fn: Ext.bind(me.onResult, me)
        });
    },

    /**
     * @param { string } button
     */
    onResult: function (button) {
        var me = this;

        if (button == 'yes') {
            me.record.set('isDefaultValue', true);
            me.oldDefaultValue.set('isDefaultValue', false);
            me.oldDefaultValue = null;
            return;
        }
        me.isDefaultField.setValue(false);
        me.record.set('isDefaultValue', false);
        me.oldDefaultValue = null;
    },

    /**
     * @param { string } name
     * @param { string } label
     * @param { boolean= } allowBlank
     * @param { boolean= } translatable
     * @returns { Ext.form.field.Text }
     */
    createTextField: function (name, label, allowBlank, translatable) {
        return Ext.create('Ext.form.field.Text', {
            fieldLabel: label,
            name: name,
            translatable: translatable,
            anchor: '100%',
            labelWidth: 130,
            allowBlank: allowBlank,
            margin: '0 3 7 0'
        });
    },

    /**
     * @returns { Shopware.apps.SwagCustomProducts.view.components.SurchargeTaxGrid }
     */
    createSurchargeGrid: function () {
        var me = this;

        return Ext.create('Shopware.apps.SwagCustomProducts.view.components.SurchargeTaxGrid', {
            parent: me.parent,
            record: me.record
        });
    },

    /**
     * @param { string } name
     * @param { string } label
     * @returns { Ext.form.field.Text }
     */
    createOrderNumberField: function (name, label) {
        var me = this,
            orderNumberField;

        orderNumberField = me.createTextField(name, label, true, false);
        orderNumberField.on('change', Ext.bind(me.validateOrderNumber, me), me, { buffer: 200 });

        orderNumberField.validator = function () {
            if (me.orderNumberIsValid) {
                return true;
            }
            return me.snippets.orderNumberFieldError;
        };

        return orderNumberField;
    },

    /**
     * @param { string } name
     * @param { string } label
     * @returns { * }
     */
    createValueField: function (name, label) {
        var me = this,
            textField;

        if (me.useColorPicker) {
            return me.createColorPicker(name, label);
        }

        if (me.useImagePicker) {
            return me.createImagePicker(name, label);
        }

        textField = me.createTextField(name, label, false, false);
        textField.on('change', Ext.bind(me.onFieldChange, me));

        return textField;
    },

    /**
     * @param { string } name
     * @param { string } label
     * @returns { Shopware.form.field.ColorField|* }
     */
    createColorPicker: function (name, label) {
        var me = this,
            colorPicker = Ext.create('Shopware.form.field.ColorField', {
                fieldLabel: label,
                name: name,
                anchor: '100%',
                labelWidth: 130,
                margin: '0 3 7 0'
            });

        colorPicker.inputField.allowBlank = false;
        colorPicker.inputField.on('change', Ext.bind(me.onFieldChange, me));

        return colorPicker;
    },

    /**
     * @param { string } name
     * @param { string } label
     * @returns { Shopware.apps.SwagCustomProducts.view.components.MediaSelection }
     */
    createImagePicker: function (name, label) {
        var me = this;

        return Ext.create('Shopware.apps.SwagCustomProducts.view.components.MediaSelection', {
            fieldLabel: label,
            name: name,
            value: false,
            anchor: '100%',
            labelWidth: 130,
            required: true,
            margin: '0 3 7 0',
            setMedia: Ext.bind(me.onFieldChange, me)
        });
    },

    /**
     * @param { string } name
     * @param { string } label
     * @returns { Ext.form.field.Checkbox }
     */
    createCheckBoxField: function (name, label) {
        var me = this;

        return Ext.create('Ext.form.field.Checkbox', {
            fieldLabel: label,
            name: name,
            anchor: '100%',
            labelWidth: 130,
            margin: '0 3 7 0',
            listeners: {
                change: Ext.bind(me.onFieldChange, me)
            }
        });
    },

    /**
     * @returns { Ext.button.Button }
     */
    createCancelButton: function () {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: me.snippets.cancelButtonText,
            cls: 'secondary',
            handler: Ext.bind(me.cancelButtonHandler, me)
        });
    },

    /**
     * @returns { Ext.button.Button }
     */
    createOkButton: function () {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: me.snippets.okButtonText,
            cls: 'primary',
            handler: Ext.bind(me.saveButtonHandler, me)
        });
    },

    /**
     * Event listener method which will be fired when the user clicks the "cancel" button.
     *
     * Resets the record and closes the window.
     */
    cancelButtonHandler: function () {
        var me = this;

        me.resetRecord();

        me.destroy();
    },

    /**
     * Event listener method which will be fired when the user clicks the "save" button.
     *
     * Validates the form. If it fails to validate, it marks the image picker, when used, otherwise it will save the record to the store.
     */
    saveButtonHandler: function () {
        var me = this;

        if (!me.formPanel.getForm().isValid()) {
            if (!me.useImagePicker) {
                return;
            }
            me.isImageSelectionInvalid();
            return;
        }

        if (!me.isRecordInStore(me.record, me.valueGrid.getStore())) {
            me.valueGrid.getStore().add(me.record);
        }

        me.destroy();
    },

    /**
     * Checks if the ImageSelection is valid.
     * If the imageSelection is invalid show a text and a red border.
     */
    isImageSelectionInvalid: function () {
        var me = this;
        if (me.valueField.isValid()) {
            me.valueField.hideIsInvalid();
            return;
        }

        me.valueField.showIsInvalid();
    },

    /**
     * @param { * } record
     * @param { Ext.data.Store } store
     * @returns { boolean }
     */
    isRecordInStore: function (record, store) {
        var isInStore = false;

        store.each(function (item) {
            if (item.internalId == record.internalId) {
                isInStore = true;
            }
        });

        return isInStore;
    },

    /**
     * Validates the last entered order number for the value.
     *
     * @param { Ext.form.field.Text } textField
     * @param { string } newValue
     */
    validateOrderNumber: function (textField, newValue) {
        var me = this;

        if (newValue == '') {
            me.orderNumberIsValid = true;
            textField.isValid();
            me.onFieldChange(textField, newValue);

            return true;
        }

        if (me.checkForNotSavedOptionsAndValues(textField, newValue)) {
            me.orderNumberIsValid = false;
            textField.isValid();
            return false;
        }

        Ext.Ajax.request({
            url: me.orderNumberValidateUrl,
            params: {
                orderNumber: newValue,
                valueId: me.record.get('id'),
                templateId: me.parent.record.get('templateId')
            },
            success: function (response) {
                var responseObj = Ext.JSON.decode(response.responseText);

                if (responseObj.success == true) {
                    me.orderNumberIsValid = true;
                    textField.isValid();
                    me.onFieldChange(textField, newValue);

                    return true;
                }

                me.orderNumberIsValid = false;
                textField.isValid();

                return false;
            }
        });
    },

    /**
     * validates the order number in new values and options, which are not saved yet
     *
     * @param { Ext.form.field.Text } textField
     * @param { string } newOrderNumber
     * @returns { boolean }
     */
    checkForNotSavedOptionsAndValues: function (textField, newOrderNumber) {
        var me = this,
            optionStore = me.optionStore,
            isOrderNumberInvalid = false,
            valuesStore,
            skipValueValidation;

        optionStore.each(function (optionItem) {
            if (optionItem.get('ordernumber') === newOrderNumber) {
                isOrderNumberInvalid = true;
                // Break, because we found the order-number and therefore a further validation is unnecessary
                return false;
            }

            valuesStore = optionItem.getValuesStore;

            if (!valuesStore || !optionItem.get('couldContainValues')) {
                // Continue, this item has no values so we don't have to check the values order-numbers
                return true;
            }

            valuesStore.each(function (valueItem) {
                skipValueValidation = me.record == valueItem;
                if (!skipValueValidation && valueItem.get('ordernumber') == newOrderNumber) {
                    isOrderNumberInvalid = true;
                    // Break
                    return false;
                }
            });

            if (isOrderNumberInvalid) {
                // Break
                return false;
            }
        });

        return isOrderNumberInvalid;
    },

    /**
     * Creates an SEO title text field
     *
     * @returns { Ext.form.field.Text }
     */
    createSeoTitleField: function () {
        var me = this;

        return Ext.create('Ext.form.field.Text', {
            fieldLabel: me.snippets.seoTitleLabel,
            name: 'seoTitle',
            labelWidth: 130,
            margin: '0 3 7 0',
            anchor: '100%',
            listeners: {
                change: Ext.bind(me.onFieldChange, me)
            }
        });
    }
});
// {/block}
