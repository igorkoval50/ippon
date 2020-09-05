// {block name="backend/swag_custom_products/view/option/types/wysiwyg"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Wysiwyg', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    /**
     * @returns { Array }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(
            me.createDefaultTextArea(),
            me.createPlaceholderTextField()
        );

        return items;
    },

    loadRecord: function () {
        var me = this;

        me.callParent(arguments);

        me.defaultValueField.setValue(me.record.get('defaultValue'));
    },

    /**
     * @returns { Ext.form.field.TextArea }
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
     * Event listener for setting the default value to the current record.
     *
     * @param { Ext.form.field.TextArea } textArea
     * @param { string } newValue
     */
    defaultValueChangeHandler: function (textArea, newValue) {
        var me = this;

        me.record.set('defaultValue', newValue);
    }
});
// {/block}
