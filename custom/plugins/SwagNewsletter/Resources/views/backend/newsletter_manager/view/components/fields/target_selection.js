//{namespace name=backend/swag_newsletter/main}
//{block name="backend/newsletter_manager/view/components/fields/target_selection"}
Ext.define('Shopware.apps.NewsletterManager.view.components.fields.TargetSelection', {
    extend: 'Ext.form.field.ComboBox',
    alias: 'widget.newsletter-components-fields-target-selection',
    name: 'target_selection',

    /**
     * Snippets for the field.
     * @object
     */
    snippets: {
        'external': '{s name=external}External{/s}',
        fields: {
            please_select: '{s name=fields/please_select}Please select...{/s}',
            category_select: '{s name=fields/category_select}Select target{/s}'
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
            queryMode: 'local',
            triggerAction: 'all',
            fieldLabel: me.snippets.fields.category_select,
            valueField: 'target',
            displayField: 'name',
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
        // we have only to possible values for the store here, so its defined locally
            store = new Ext.data.ArrayStore({
                fields: ['id', 'target', 'name'],
                data: [
                    [1, '_blank', me.snippets.external],
                    [2, '_parent', 'Shopware']
                ]
            });

        store.load({
            callback: function () {
                //if the component wasn't fully realized, yet, getValue() will fail.
                if (!this.displayTpl) {
                    return;
                }
                var record, value = me.getValue();

                if (value) {
                    record = store.findRecord('target', value);
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