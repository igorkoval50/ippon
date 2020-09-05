// {block name="backend/swag_business_essentials/view/components/grid_field"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.components.GridField', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.business_essentials-grid_field',
    name: 'whiteListedControllers',
    itemId: 'grid_field',
    mixins: {
        field: 'Ext.form.field.Field'
    },

    /**
     * Properly sets the value into the grid.
     *
     * @param { Array } values
     * @param { boolean } keepStore
     */
    setValue: function(values, keepStore) {
        var me = this,
            model;

        keepStore = keepStore || false;

        if (!keepStore || !values || values.length === 0) {
            me.store.removeAll();
        }

        Ext.each(values, function(item) {
            if (!item || me.store.getById(item.key)) {
                return;
            }

            model = me.store.model.create(item);
            me.store.add(model);
        });
    },

    /**
     * Adds a single record to the grid-field.
     *
     * @param { Ext.data.Model } record
     */
    addRecord: function(record) {
        this.setValue([record.getData()], true);
    }
});
// {/block}
