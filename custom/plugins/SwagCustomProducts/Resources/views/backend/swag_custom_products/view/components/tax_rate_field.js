// {namespace name="backend/swag_custom_products/option/fields"}
// {block name="backend/swag_custom_products/components/taxRateField"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.TaxRateField', {
    extend: 'Ext.form.field.ComboBox',

    fieldLabel: '{s name="surcharge/tax/rate/title"}Tax rate{/s}',
    storeUrl: '{url controller="SwagCustomProducts" action="getTaxes"}',

    labelWidth: 130,
    anchor: '100%',

    displayField: 'name',
    valueField: 'id',

    autoload: false,

    /**
     * init the component
     */
    initComponent: function () {
        var me = this;

        me.createStore();

        me.callParent(arguments);
    },

    /**
     * create the taxRate store and load them.
     */
    createStore: function () {
        var me = this;

        me.store = Ext.create('Shopware.apps.Base.store.Tax').load({
            callback: Ext.bind(me.afterLoadStore, me)
        });
    },

    /**
     * set the value of select them after load
     * because before there is only the id displayed
     */
    afterLoadStore: function () {
        var me = this;

        if (me.selectedValue) {
            me.select(me.store.findRecord('id', me.selectedValue));
            return;
        }

        me.select(me.getStore().getAt(0));
    },

    /**
     * @overwrite
     * @param { int } value
     */
    setValue: function (value) {
        var me = this;

        me.selectedValue = value;

        me.callParent(arguments);
    }
});
// {/block}
