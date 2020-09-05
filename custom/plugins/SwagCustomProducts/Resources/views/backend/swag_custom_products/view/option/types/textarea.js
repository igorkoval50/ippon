// {block name="backend/swag_custom_products/view/option/types/textarea"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Textarea', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    /**
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(
            me.createDefaultTextArea(),
            me.createMaxTextLength(),
            me.createPlaceholderTextField()
        );

        return items;
    },

    loadRecord: function () {
        var me = this;

        me.callParent(arguments);

        me.defaultValueField.setValue(me.record.get('defaultValue'));
        me.maxTextLengthField.setValue(me.record.get('maxTextLength'));
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createDefaultTextArea: function () {
        var me = this;

        me.defaultValueField = Ext.create('Ext.form.field.TextArea', {
            name: 'defaultValue',
            fieldLabel: me.snippets.textfield.defaultValue.label,
            translatable: true,
            flex: 1,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            listeners: {
                change: Ext.bind(me.defaultValueChangeHandler, me)
            }
        });

        return me.defaultValueField;
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
            negativeText: me.snippets.textarea.maxTextLength.negativeText,
            listeners: {
                change: Ext.bind(me.maxTextLengthChangeHandler, me)
            }
        });

        return me.maxTextLengthField;
    },

    /**
     * @param { Ext.form.field.TextArea } textArea
     * @param { string } newValue
     */
    defaultValueChangeHandler: function (textArea, newValue) {
        var me = this;

        me.record.set('defaultValue', newValue);
    },

    /**
     * @param { Ext.form.field.Number } numberField
     * @param { int } newValue
     */
    maxTextLengthChangeHandler: function (numberField, newValue) {
        var me = this;

        me.record.set('maxTextLength', newValue);
    }
});
// {/block}
