// {block name="backend/swag_custom_products/view/option/types/date"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Date', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    /**
     * @overwrite
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(
            me.createMinDateField(),
            me.createMaxDateField(),
            me.createPlaceholderTextField()
        );

        return items;
    },

    /**
     * @returns { Ext.form.field.Date | * }
     */
    createMinDateField: function () {
        var me = this;

        me.minDateField = Ext.create('Ext.form.field.Date', {
            name: 'minDate',
            fieldLabel: me.snippets.date.min.label,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            listeners: {
                change: Ext.bind(me.setValueToRecord, me)
            }
        });

        return me.minDateField;
    },

    /**
     * @returns { Ext.form.field.Date | * }
     */
    createMaxDateField: function () {
        var me = this;

        me.maxDateField = Ext.create('Ext.form.field.Date', {
            name: 'maxDate',
            fieldLabel: me.snippets.date.max.label,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            listeners: {
                change: Ext.bind(me.setValueToRecord, me)
            }
        });

        return me.maxDateField;
    },

    /**
     * @param dateField
     * @param newValue
     */
    setValueToRecord: function (dateField, newValue) {
        var me = this;

        me.record.set(dateField.getName(), newValue);
    }
});
// {/block}
