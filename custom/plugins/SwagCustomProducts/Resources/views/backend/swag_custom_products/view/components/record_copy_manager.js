// {block name="backend/swag_custom_products/components/recordCopyManager"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.RecordCopyManager', {
    extend: 'Ext.Component',

    /**
     * create a copy of the given record.
     *
     * @param { Ext.data.Model } record
     * @returns { record.$className }
     */
    copy: function (record) {
        var recordCopy = Ext.create(record.$className);

        record.fields.each(function (field) {
            field = field.name;
            recordCopy.set(field, record.get(field));
        });

        return recordCopy;
    },

    /**
     * Reset a given record to the given copy.
     *
     * @param { Ext.data.Model } record
     * @param { Ext.data.Model } recordCopy
     * @returns { Ext.data.Model }
     */
    resetRecord: function (record, recordCopy) {
        record.fields.each(function (field) {
            field = field.name;
            record.set(field, recordCopy.get(field));
        });

        record.getPrices().rejectChanges();

        return record;
    }
});
// {/block}
