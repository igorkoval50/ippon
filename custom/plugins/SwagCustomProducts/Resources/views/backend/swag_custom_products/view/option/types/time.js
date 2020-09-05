// {block name="backend/swag_custom_products/view/option/types/time"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Time', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    /**
     * @overwrite
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(
            me.createMinTimeField(),
            me.createMaxTimeField(),
            me.createPlaceholderTextField()
        );

        return items;
    },

    /**
     * @returns { Ext.form.field.Time | * }
     */
    createMinTimeField: function () {
        var me = this;

        me.minDateField = Ext.create('Ext.form.field.Time', {
            name: 'minDate',
            fieldLabel: me.snippets.time.min.label,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            increment: 30,
            listeners: {
                change: Ext.bind(me.setValueToRecord, me)
            }
        });

        return me.minDateField;
    },

    /**
     * @returns { Ext.form.field.Time | * }
     */
    createMaxTimeField: function () {
        var me = this;

        me.maxDateField = Ext.create('Ext.form.field.Time', {
            name: 'maxDate',
            fieldLabel: me.snippets.time.max.label,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            increment: 30,
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
