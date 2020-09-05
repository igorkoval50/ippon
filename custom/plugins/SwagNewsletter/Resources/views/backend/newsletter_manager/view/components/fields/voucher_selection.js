//{namespace name=backend/swag_newsletter/main}
//{block name="backend/newsletter_manager/view/components/fields/voucher_selection"}
Ext.define('Shopware.apps.NewsletterManager.view.components.fields.VoucherSelection', {
    extend: 'Ext.form.field.ComboBox',
    alias: 'widget.newsletter-components-fields-voucher-selection',
    name: 'voucher_selection',

    /**
     * Snippets for the field.
     * @object
     */
    snippets: {
        fields: {
            please_select: '{s name=fields/please_select}Please select...{/s}',
            label: '{s name=fields/voucher_label}Select voucher{/s}'
        }
    },

    /**
     * Initiliaze the component.
     *
     * @public
     * @return void
     */
    initComponent: function () {
        var me = this;

        Ext.apply(me, {
            triggerAction: 'all',
            fieldLabel: me.snippets.fields.label,
            valueField: 'id',
            displayField: 'description',
            emptyText: me.snippets.fields.please_select,
            store: me.createStore()
        });

        me.callParent(arguments);
    },

    /**
     * Creates a store which will be used
     * for the combo box.
     *
     * @public
     * @return [object] Ext.data.Store
     */
    createStore: function () {
        var me = this,
            store = Ext.create('Shopware.apps.NewsletterManager.store.Voucher');

        store.load({
            callback: function () {
                //if the component wasn't fully realized, yet, getValue() will fail.
                if (!this.displayTpl) {
                    return;
                }
                var record, value = me.getValue();

                if (value) {
                    record = store.findRecord('id', value);
                }
                if (!record) {
                    record = store.first();
                }
                me.select(record);
            }
        });

        return store;
    }
});
//{/block}
