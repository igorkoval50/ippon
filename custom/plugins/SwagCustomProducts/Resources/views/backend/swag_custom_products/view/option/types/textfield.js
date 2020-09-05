// {block name="backend/swag_custom_products/view/option/types/textfield"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Textfield', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    /**
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(
            me.createDefaultTextField(),
            me.createMaxTextLength(),
            me.createPlaceholderTextField()
        );

        return items;
    },

    loadRecord: function () {
        var me = this;

        me.callParent(arguments);

        me.defaultTextField.setValue(me.record.get('defaultValue'));
        me.maxTextLengthField.setValue(me.record.get('maxTextLength'));
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createDefaultTextField: function () {
        var me = this;

        me.defaultTextField = Ext.create('Ext.form.field.Text', {
            name: 'defaultValue',
            fieldLabel: me.snippets.textfield.defaultValue.label,
            flex: 1,
            translatable: true,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            listeners: {
                change: Ext.bind(me.defaultTextChangeHandler, me)
            }
        });

        return me.defaultTextField;
    },

    /**
     * @returns { Ext.form.field.Number }
     */
    createMaxTextLength: function () {
        var me = this;

        me.maxTextLengthField = Ext.create('Ext.form.field.Number', {
            name: 'maxTextLength',
            fieldLabel: me.snippets.textfield.maxTextLength.label,
            flex: 1,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            minValue: 0,
            negativeText: me.snippets.textfield.maxTextLength.negativeText,
            listeners: {
                change: Ext.bind(me.maxLengthChangeHandler, me)
            }
        });

        return me.maxTextLengthField;
    },

    /**
     * @param { Ext.form.field.Text } textField
     * @param { string } newValue
     */
    defaultTextChangeHandler: function (textField, newValue) {
        var me = this;

        me.record.set('defaultValue', newValue);
    },

    /**
     * @param { Ext.form.field.Number } numberField
     * @param { int } newValue
     */
    maxLengthChangeHandler: function (numberField, newValue) {
        var me = this;

        me.record.set('maxTextLength', newValue);
    }
});
// {/block}
