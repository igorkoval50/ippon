// {block name="backend/swag_custom_products/view/option/types/numberfield"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Numberfield', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    /**
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(
            me.createMinNumberField(0),
            me.createMaxNumberField(),
            me.createIntervalNumberField(),
            me.createPlaceholderTextField()
        );

        return items;
    },

    loadRecord: function () {
        var me = this;

        me.callParent(arguments);

        me.minNumberField.setValue(me.record.get('minValue'));
        me.maxNumberField.setValue(me.record.get('maxValue'));
        me.internvalNumberField.setValue(me.record.get('interval'));
    },

    /**
     * Creates the input field for the max number field.
     *
     * @param { int } value
     * @returns { Ext.form.field.Number }
     */
    createMinNumberField: function (value) {
        var me = this;

        if (isNaN(value)) {
            throw new Error('Number expected, got ' + typeof value);
        }

        me.minNumberField = Ext.create('Ext.form.field.Number', {
            name: 'minValue',
            fieldLabel: me.snippets.number.minValue.label,
            flex: 1,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            value: value,
            validator: Ext.bind(me.minNumberFieldValidator, me),
            listeners: {
                change: Ext.bind(me.minNumberfieldChangeHandler, me)
            }
        });

        return me.minNumberField;
    },

    /**
     * Creates the input field for the max number field.
     *
     * @returns { Ext.form.field.Number }
     */
    createMaxNumberField: function () {
        var me = this;

        me.maxNumberField = Ext.create('Ext.form.field.Number', {
            name: 'maxValue',
            fieldLabel: me.snippets.number.maxValue.label,
            flex: 1,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            validator: Ext.bind(me.maxNumberFieldValidator, me),
            listeners: {
                change: Ext.bind(me.maxNumberFieldChangeHandler, me)
            }
        });

        return me.maxNumberField;
    },

    /**
     * Creates the interval validator number field for the number option type.
     *
     * @returns { Ext.form.field.Number }
     */
    createIntervalNumberField: function () {
        var me = this;

        me.internvalNumberField = Ext.create('Ext.form.field.Number', {
            name: 'interval',
            fieldLabel: me.snippets.number.interval.label,
            helpText: me.snippets.number.interval.helpText,
            flex: 1,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            allowBlank: false,
            validator: Ext.bind(me.intervalFieldValidator, me),
            listeners: {
                change: Ext.bind(me.intervalFieldChangeHandler, me)
            }
        });

        return me.internvalNumberField;
    },

    /**
     * @param { Ext.form.field.Number } numberField
     * @param { int } newValue
     */
    minNumberfieldChangeHandler: function (numberField, newValue) {
        var me = this;

        me.record.set('minValue', newValue);
    },

    /**
     * @param { Ext.form.field.Number } numberField
     * @param { int } newValue
     */
    maxNumberFieldChangeHandler: function (numberField, newValue) {
        var me = this;

        me.record.set('maxValue', newValue);
    },

    /**
     * @param { Ext.form.field.Number } numberField
     * @param { float } newValue
     */
    intervalFieldChangeHandler: function (numberField, newValue) {
        var me = this;

        me.record.set('interval', me.convertToFloat(newValue));
    },

    /**
     * @returns { boolean | string }
     */
    minNumberFieldValidator: function () {
        var me = this;

        if (me.minNumberField.getValue() < me.maxNumberField.getValue() || me.maxNumberField.getValue() == null) {
            return true;
        }

        return me.snippets.number.minValue.error;
    },

    /**
     * @returns { boolean | string }
     */
    maxNumberFieldValidator: function () {
        var me = this;

        if (me.maxNumberField.getValue() > me.minNumberField.getValue() || me.maxNumberField.getValue() == null) {
            return true;
        }

        return me.snippets.number.maxValue.error;
    },

    /**
     * @param { string } value
     * @returns { boolean | string }
     */
    intervalFieldValidator: function (value) {
        var me = this;
        value = me.convertToFloat(value);

        if (!value) {
            return true;
        }

        if (!me.maxNumberField.getValue()) {
            return true;
        }

        me.minMaxDifference = me.maxNumberField.getValue() - me.minNumberField.getValue();
        if (value <= me.minMaxDifference) {
            return true;
        }

        return me.snippets.number.interval.error;
    },

    /**
     * Convert a string that might contain commas as a decimal separator to a float value
     *
     * @param { string } value
     */
    convertToFloat: function (value) {
        if (!value) {
            return;
        }

        if (typeof value !== 'string') {
            return value;
        }

        return window.parseFloat(value.replace(',', '.'));
    }
});
// {/block}
