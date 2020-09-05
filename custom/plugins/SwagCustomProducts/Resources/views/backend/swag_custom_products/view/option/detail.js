//

//{namespace name="backend/swag_custom_products/option/detail"}
//{block name="backend/swag_custom_products/view/option/detail"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.Detail', {
    extend: 'Shopware.model.Container',
    alias: 'widget.swag-custom-products-option-detail',

    flex: 1,

    modelFieldSet: null,

    layout: {
        type: 'hbox',
        align: 'stretch'
    },

    snippets: {
        types: {
            //{block name="backend/swag_custom_products/model/option/type/snippets"}{/block}
            checkbox: '{s name="combo/value/name/checkbox"}Checkbox{/s}',
            multiselect: '{s name="combo/value/name/multiselect"}Multiselect{/s}',
            numberfield: '{s name="combo/value/name/numberfield"}Numberfield{/s}',
            radio: '{s name="combo/value/name/radio"}RadioSelect{/s}',
            select: '{s name="combo/value/name/select"}Combobox{/s}',
            textarea: '{s name="combo/value/name/textarea"}Textarea{/s}',
            textfield: '{s name="combo/value/name/textfield"}Text{/s}',
            colorselect: '{s name="combo/value/name/colorselection"}Color selection{/s}',
            imageselect: '{s name="combo/value/name/imageselection"}Image selection{/s}',
            fileupload: '{s name="combo/value/name/fileupload"}File upload{/s}',
            imageupload: '{s name="combo/value/name/imageupload"}Image upload{/s}',
            wysiwyg: '{s name="combo/value/name/wysiwyg"}HTML editor{/s}'
        },
        field: {
            name: {
                label: '{s name="field/name/label"}Name{/s}'
            },
            description: {
                label: '{s name="field/description/label"}Description{/s}'
            },
            typeId: {
                label: '{s name="field/type_id/label"}Type{/s}',
                msgBox: {
                    title: '{s name="field/type_id/msgbox/title"}Change option type{/s}',
                    message: '{s name="field/type_id/msgbox/message"}If you change the option type all settings will be deleted for this option. Do you really want to change the option type?{/s}'
                }
            },
            required: {
                label: '{s name="field/required/label"}Required field{/s}'
            },
            position: {
                label: '{s name="field/position/label"}Position{/s}'
            },
            orderNumber: {
                label: '{s name="field/orderNumber/label"}Order number{/s}',
                error: '{s name="field/orderNumber/error"}This order number is already in use.{/s}'
            }
        }
    },

    /**
     * Defines the field set and its fields
     */
    configure: function () {
        var me = this;

        return {
            splitFields: false,
            fieldSets: [
                {
                    title: null,
                    flex: 1,
                    border: false,
                    autoScroll: true,
                    fields: {
                        name: me.createNameField,
                        description: me.createDescriptionField,
                        type: me.createTypeField,
                        ordernumber: me.createOrderNumberField,
                        required: me.createRequiredField,
                        typeContainer: me.createTypeContainer
                    }
                }
            ]
        }
    },

    initComponent: function () {
        var me = this;

        me.orderNumberIsValid = true;
        me.orderNumberValidateUrl = '{url controller="SwagCustomProducts" action="validateOptionOrderNumberAjax"}';

        me.callParent(arguments);
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createNameField: function () {
        var me = this;

        me.nameField = Ext.create('Ext.form.field.Text', {
            name: 'name',
            fieldLabel: me.snippets.field.name.label,
            translatable: true,
            labelWidth: 130,
            allowBlank: false,
            anchor: '100%',
            margin: '0 3 7 0',
            listeners: {
                change: Ext.bind(me.nameFieldChangeListener, me)
            }
        });

        return me.nameField;
    },

    /**
     * @returns { Shopware.form.field.TinyMCE | * }
     */
    createDescriptionField: function () {
        var me = this;

        me.descriptionField = Ext.create('Shopware.form.field.TinyMCE', {
            fieldLabel: me.snippets.field.description.label,
            translatable: true,
            labelWidth: 130,
            margin: '0 3 7 0',
            anchor: '100%',
            name: 'description',
            listeners: {
                afterRender: Ext.bind(me.afterInitTinyMCE, me)
            }
        });

        return me.descriptionField;
    },

    /**
     * Creates the custom type field which will be used to switch between different option types.
     *
     * @returns { Ext.form.field.ComboBox }
     */
    createTypeField: function () {
        var me = this;

        me.typeField = Ext.create('Ext.form.field.ComboBox', {
            fieldLabel: me.snippets.field.typeId.label,
            name: 'type',
            displayField: 'name',
            valueField: 'type',
            forceSelection: true,
            labelWidth: 130,
            margin: '0 3 7 0',
            anchor: '100%',
            allowBlank: false,
            store: me.getTypeStore(),
            listeners: {
                change: Ext.bind(me.addTypeForm, me)
            }
        });

        return me.typeField;
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createOrderNumberField: function () {
        var me = this;

        me.orderNumberField = Ext.create('Ext.form.field.Text', {
            fieldLabel: me.snippets.field.orderNumber.label,
            labelWidth: 130,
            name: 'ordernumber',
            anchor: '100%',
            margin: '0 3 7 0',
            listeners: {
                change: {
                    fn: me.validateOrderNumber,
                    buffer: 200,
                    scope: me
                }
            },
            validator: function () {
                if (me.orderNumberIsValid) {
                    return true;
                }
                return me.snippets.field.orderNumber.error;
            }
        });

        return me.orderNumberField;
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createRequiredField: function () {
        var me = this;

        me.requiredField = Ext.create('Ext.form.field.Checkbox', {
            fieldLabel: me.snippets.field.required.label,
            name: 'required',
            labelWidth: 130,
            anchor: '100%',
            margin: '0 3 7 0',
            listeners: {
                change: Ext.bind(me.requiredFieldChangeListener, me)
            }
        });

        return me.requiredField;
    },

    /**
     * @returns { Ext.container.Container }
     */
    createTypeContainer: function () {
        var me = this;

        me.typeContainer = Ext.create('Ext.container.Container', {
            layout: 'anchor',
            name: 'typeContainer'
        });

        return me.typeContainer;
    },

    /**
     * @param { Ext.form.field.Text } textField
     * @param { string } newValue
     */
    nameFieldChangeListener: function (textField, newValue) {
        var me = this;

        me.record.set('name', newValue);
    },

    /**
     * @param { Ext.form.field.Checkbox } checkbox
     * @param { boolean } newValue
     */
    requiredFieldChangeListener: function (checkbox, newValue) {
        var me = this;

        me.record.set('required', newValue);
    },

    /**
     * @param { Ext.form.field.Text } textField
     * @param { string } newValue
     */
    orderNumberFieldChangeListener: function (textField, newValue) {
        var me = this;

        me.record.set('ordernumber', newValue);
    },

    /**
     * @returns { Ext.data.Store }
     */
    getTypeStore: function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            fields: ['name', 'type', 'couldContainValues'],
            proxy: {
                type: 'ajax',
                api: {
                    read: '{url controller="SwagCustomProducts" action="getOptionTypes"}'
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                load: Ext.bind(me.enrichWithNames, me)
            }
        }).load();
    },

    /**
     * set the translated name to the models
     */
    enrichWithNames: function () {
        var me = this,
            typeTranslator = Ext.create('Shopware.apps.SwagCustomProducts.view.components.TypeTranslator');

        me.typeField.getStore().each(function (item) {
            item.set('name', typeTranslator.getTranslation(item.get('type')));
        });
    },

    /**
     * adds the fields which are required for a specific option type
     *
     * @param combo
     * @param { string } newValue
     */
    addTypeForm: function (combo, newValue) {
        var me = this;

        // check if the newValue is valid to prevent exceptions
        if (!me.isType(newValue)) {
            return;
        }

        // if the user changes the option type, he gets asked if he really want to do that
        if (me.record.get('type') !== '' && newValue !== me.record.get('type')) {
            Ext.Msg.confirm(
                me.snippets.field.typeId.msgBox.title,
                me.snippets.field.typeId.msgBox.message,
                function (answer) {
                    if (answer !== 'yes') {
                        combo.setValue(me.record.get('type'));

                        return;
                    }

                    me.resetOptionSpecificRecordValues();
                    me.setNewType(combo, newValue);
                }
            );

            return;
        }

        me.setNewType(combo, newValue);
    },

    /**
     * @param combo
     * @param { string } newValue
     */
    setNewType: function (combo, newValue) {
        var me = this,
            type = newValue.charAt(0).toUpperCase() + newValue.slice(1),
            newType = 'Shopware.apps.SwagCustomProducts.view.option.types.' + type,
            selectedModel = combo.findRecordByValue(newValue),
            addForm;

        addForm = Ext.create(newType, {
            record: me.record
        });

        me.typeContainer.removeAll();
        me.typeContainer.add(addForm);
        me.initTranslationPluginForAdds();

        me.record.set('type', newValue);
        me.record.set('couldContainValues', selectedModel.get('couldContainValues'));
    },

    /**
     * @param { string } newValue
     * @returns { boolean }
     */
    isType: function (newValue) {
        var me = this,
            found = false;

        me.typeField.getStore().each(function (item) {
            if (item.get('type') == newValue) {
                found = true;
            }
        });

        return found
    },

    /**
     * reset the values which are used by specific options,
     * to prevent problems if the user changes the option type
     */
    resetOptionSpecificRecordValues: function () {
        var me = this;

        me.record.set({
            defaultValue: null,
            placeholder: null,
            isOnceSurcharge: false,
            maxTextLength: null,
            minValue: null,
            maxValue: null,
            maxFiles: 1,
            interval: null
        });
    },

    /**
     * ReInit the Translation fields
     */
    initTranslationPluginForAdds: function () {
        var me = this,
            formPanel = me.up('[xtype=form]');

        if (!formPanel.translationPlugin) {
            return;
        }

        formPanel.translationPlugin.initTranslationFields(formPanel);
    },

    /**
     * Validates the last entered order number for the option.
     *
     * @param { Ext.form.field.Text } textField
     * @param { string } newValue
     */
    validateOrderNumber: function (textField, newValue) {
        var me = this;

        if (newValue == '') {
            me.orderNumberIsValid = true;
            textField.isValid();
            me.orderNumberFieldChangeListener(textField, newValue);

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
                optionId: me.record.get('id'),
                templateId: me.record.get('templateId')
            },
            success: function (response) {
                var responseObj = Ext.JSON.decode(response.responseText);

                if (responseObj.success == true) {
                    me.orderNumberIsValid = true;
                    textField.isValid();
                    me.orderNumberFieldChangeListener(textField, newValue);

                    return true;
                }

                me.orderNumberIsValid = false;
                textField.isValid();

                return false;
            }
        });
    },

    /**
     * validates the order number in new options and values, which are not saved yet
     *
     * @param { Ext.form.field.Text } textField
     * @param { string } newOrderNumber
     * @returns { boolean }
     */
    checkForNotSavedOptionsAndValues: function (textField, newOrderNumber) {
        var me = this,
            optionStore = me.up('window').optionStore,
            isOrderNumberInvalid = false,
            valuesStore,
            skipOptionValidation;

        optionStore.each(function (optionItem) {
            skipOptionValidation = optionItem == me.record;

            if (!skipOptionValidation && optionItem.get('ordernumber') === newOrderNumber) {
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
                if (valueItem.get('ordernumber') == newOrderNumber) {
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
     * @param { * } editor
     */
    afterInitTinyMCE: function (editor) {
        var me = this;

        editor.tinymce.onChange.add(function (ed, values) {
            if (me.isHtmlStringIsNullOrEmpty(values.content)) {
                me.record.set('description', null);
                return;
            }

            me.record.set('description', values.content);
        });
    },

    /**
     * Checks the content of the TinyMCE for a empty string.
     * This method replace all HTML tags and whitespaces to check if there a empty string
     *
     * @param { string } htmlString
     * @returns { boolean }
     */
    isHtmlStringIsNullOrEmpty: function (htmlString) {
        var startTags = /[<][a-zA-Z-:;="0-9 ,()#]*[\/]?[>]/g,
            endTags = /[<][\/][a-zA-Z-:;="0-9 ,()]*[>]/g,
            whiteSpace = /\A[\s]+|[\s]+$|[ ]/g,
            htmlEntities = /[&][a-z]*[;]/g;

        htmlString = htmlString.replace(startTags, '')
            .replace(endTags, '')
            .replace(whiteSpace, '')
            .replace(htmlEntities, '');

        return !htmlString;
    }
});
//{/block}
