// {namespace name="backend/swag_custom_products/option/fields"}
// {block name="backend/swag_custom_products/view/option/types/abstract_type_container"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer', {
    extend: 'Ext.container.Container',

    id: 'type-base-container',

    width: '100%',

    anchor: '100%',

    layout: 'fit',

    /**
     * @type { Ext.data.Model }
     */
    record: null,

    /**
     * Holds all fields which belongs to the payload and not directly to the Shopware.apps.SwagCustomProducts.model.option.Value.
     * Can be passed to configure own payload fields for the value grid.
     *
     * @type { Array }
     */
    payloadFields: ['value'],

    /**
     * Holds the margins, labelWidth etc for the form fields
     *
     * @type Object
     */
    designVars: {
        labelWidth: 130,
        margin: '0 3 7 0',
        anchor: '100%'
    },

    /**
     * @type Object
     */
    snippets: {
        default: {
            placeholder: {
                label: "{s name='default/placeholder/label'}Placeholder{/s}"
            }
        },
        textfield: {
            defaultValue: {
                label: "{s name='textfield/default_value/label'}Default text{/s}"
            },
            maxTextLength: {
                label: "{s name='textfield/max_text_length/label'}Max text length{/s}",
                negativeText: "{s name='textfield/max_text_length/negative_text'}The value cannot be negative!{/s}"
            }
        },
        textarea: {
            maxTextLength: {
                negativeText: "{s name='textfield/max_text_length/negative_text'}The value cannot be negative!{/s}"
            }
        },
        number: {
            defaultValue: {
                label: "{s name='number/default_value/label'}Default value{/s}",
                minValueError: "{s name='number/default_value/min_value_error'}The default value can't be less than the min value.{/s}",
                maxValueError: "{s name='number/default_value/max_value_error'}The default value can't be greater than the max value.{/s}"
            },
            minValue: {
                label: "{s name='number/min_value/label'}Min value{/s}",
                error: "{s name='number/min_value/error'}The min value can't be greater than the max value.{/s}"
            },
            maxValue: {
                label: "{s name='number/max_value/label'}Max value{/s}",
                error: "{s name='number/max_value/error'}The max value can't be less than the min value.{/s}"
            },
            interval: {
                label: "{s name='number/interval/label'}Interval{/s}",
                helpText: "{s name='number/interval/help_text'}You can define the interval of numbers. I.e. you enter 0.5 the user can only select 0, 0.5, 1.0, 1.5, etc.{/s}",
                error: "{s name='number/interval/error'}The interval can't be greater than the difference between the min and max value.{/s}"
            }
        },
        date: {
            min: {
                label: "{s name='date/label/min'}Minimum date{/s}"
            },
            max: {
                label: "{s name='date/label/max'}Maximum date{/s}"
            }
        },
        fileUpload: {
            maxFileSize: {
                label: "{s name='file_upload/max_file_size/label'}Max file size (MB){/s}"
            },
            maxUploadFiles: {
                label: "{s name='file_upload/max_files/label'}Max number of files{/s}"
            }
        },
        time: {
            min: {
                label: "{s name='time/label/min'}Start time{/s}"
            },
            max: {
                label: "{s name='time/label/max'}End time{/s}"
            }
        },
        values: {
            grid: {
                title: "{s name='values/grid/title'}Add values{/s}",
                header: {
                    name: '{s name="values/grid/header/name"}Name{/s}',
                    value: '{s name="values/grid/header/value"}Value{/s}',
                    position: '{s name="values/grid/header/position"}Position{/s}',
                    defaultValue: '{s name="values/grid/header/defaultValue"}Default value{/s}'
                }
            }
        },

        textFieldLabel: '{s name="surcharge/title"}Surcharge{/s}',
        onceSurchargeLabel: '{s name="once/surcharge/title"}Once surcharge{/s}',
        allowsMultipleSelectionLabel: '{s name="allows_multiple_selection/title"}Multiple selection{/s}'
    },

    initComponent: function () {
        var me = this;

        me.items = me.createItems();

        me.callParent(arguments);

        me.loadRecord();
    },

    /**
     * Method which can be overridden to add own form fields
     *
     * @example:
     * return [
     *      Ext.create('Ext.form.field.Textfield', {
     *          name: 'exampleField',
     *          fieldLabel: 'example field',
     *          flex: 1,
     *          labelWidth: me.designVars.labelWidth,
     *          margin: me.designVars.margin,
     *          anchor: me.designVars.anchor
     *      }),
     *      me.createCustomField()
     * ];
     *
     * @returns { Array }
     */
    createItems: function () {
        var me = this;

        return [
            me.createSurchargeTaxRateField(),
            me.createOnceSurchargeField()
        ];
    },

    /**
     * Load the values of the record in the fields
     */
    loadRecord: function () {
        var me = this;

        me.record.fields.each(function (item) {
            var field = me.down('[name=' + item.name + ']');
            if (field) {
                field.setValue(me.record.get(item.name));
            }
        });
    },

    /**
     * Creates the placeholder textfield.
     *
     * @returns { Ext.form.field.Text }
     */
    createPlaceholderTextField: function () {
        var me = this;

        me.placeholderField = Ext.create('Ext.form.field.Text', {
            name: 'placeholder',
            fieldLabel: me.snippets.default.placeholder.label,
            flex: 1,
            translatable: true,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            listeners: {
                change: Ext.bind(me.placeholderChangeHandler, me)
            }
        });

        return me.placeholderField;
    },

    /**
     * @returns { Shopware.apps.SwagCustomProducts.view.components.TaxRateField }
     */
    createSurchargeTaxRateField: function () {
        var me = this;

        me.surchargeTaxRateField = Ext.create('Shopware.apps.SwagCustomProducts.view.components.SurchargeTaxGrid', {
            parent: me,
            record: me.record
        });

        return me.surchargeTaxRateField;
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createOnceSurchargeField: function () {
        var me = this;

        me.onceSurchargeField = Ext.create('Ext.form.field.Checkbox', {
            fieldLabel: me.snippets.onceSurchargeLabel,
            name: 'isOnceSurcharge',
            labelWidth: 130,
            anchor: '100%',
            margin: '0 3 7 0',
            listeners: {
                change: Ext.bind(me.onceSurchargeFieldChangeHandler, me)
            }
        });

        return me.onceSurchargeField;
    },

    /**
     * @param { Ext.form.field.Text } textField
     * @param { string } newValue
     */
    placeholderChangeHandler: function (textField, newValue) {
        var me = this;

        me.record.set('placeholder', newValue);
    },

    /**
     * @param { Ext.form.field.Checkbox } checkboxField
     * @param { boolean } newValue
     */
    onceSurchargeFieldChangeHandler: function (checkboxField, newValue) {
        var me = this;

        me.record.set('isOnceSurcharge', newValue);
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createAllowsMultipleSelectionField: function () {
        var me = this;

        me.allowsMultipleSelectionField = Ext.create('Ext.form.field.Checkbox', {
            fieldLabel: me.snippets.allowsMultipleSelectionLabel,
            name: 'allowsMultipleSelection',
            labelWidth: 130,
            inputValue: true,
            uncheckedValue: false,
            anchor: '100%',
            margin: '0 3 7 0',
            listeners: {
                change: Ext.bind(me.allowsMultipleSelectionChangeHandler, me)
            }
        });

        return me.allowsMultipleSelectionField;
    },
    /**
     * @param { Ext.form.field.Checkbox } checkboxField
     * @param { boolean } newValue
     */
    allowsMultipleSelectionChangeHandler: function (checkboxField, newValue) {
        var me = this;

        me.record.set('allowsMultipleSelection', newValue);
    }
});
// {/block}
