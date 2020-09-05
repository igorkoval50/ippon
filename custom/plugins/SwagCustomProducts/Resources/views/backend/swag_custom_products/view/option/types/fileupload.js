// {block name="backend/swag_custom_products/view/option/types/fileupload"}
Ext.define('Shopware.apps.SwagCustomProducts.view.option.types.Fileupload', {
    extend: 'Shopware.apps.SwagCustomProducts.view.option.types.AbstractTypeContainer',

    /**
     * @returns { *[] }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(me.createMaxUploadsField());
        items.push(me.createMaxFileSizeField());

        return items;
    },

    /**
     * @returns { Ext.form.field.Number }
     */
    createMaxUploadsField: function () {
        var me = this;

        me.maxFileUploads = Ext.create('Ext.form.field.Number', {
            name: 'maxFiles',
            fieldLabel: me.snippets.fileUpload.maxUploadFiles.label,
            flex: 1,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            minValue: 1,
            value: 1,
            listeners: {
                change: Ext.bind(me.onMaxFileChange, me)
            }
        });

        return me.maxFileUploads;
    },

    /**
     * @returns { Ext.form.field.Number }
     */
    createMaxFileSizeField: function () {
        var me = this;

        me.maxFileSizeMB = Ext.create('Ext.form.field.Number', {
            name: 'maxFileSizeMB',
            fieldLabel: me.snippets.fileUpload.maxFileSize.label,
            flex: 1,
            allowBlank: false,
            labelWidth: me.designVars.labelWidth,
            margin: me.designVars.margin,
            anchor: me.designVars.anchor,
            minValue: 0.1,
            listeners: {
                change: Ext.bind(me.onMaxFileSizeChange, me)
            }
        });

        return me.maxFileSizeMB;
    },

    loadRecord: function () {
        var me = this, maxFileSize;

        me.callParent(arguments);

        maxFileSize = me.convertBytesToMB(me.record.get('maxFileSize'));
        me.maxFileSizeMB.setValue(maxFileSize);
    },

    /**
     *
     * @param { Ext.form.field.Number } field
     * @param newValue
     */
    onMaxFileSizeChange: function (field, newValue) {
        var me = this, maxFileSizeBytes;

        maxFileSizeBytes = me.convertMBtoBytes(newValue);
        me.record.set('maxFileSize', maxFileSizeBytes);
    },

    /**
     * @param { number } value
     * @returns { number }
     */
    convertMBtoBytes: function (value) {
        return value * 1024 * 1024;
    },

    /**
     * @param { number } value
     * @returns { number }
     */
    convertBytesToMB: function (value) {
        return value / 1024 / 1024;
    },

    /**
     * @param { Ext.form.field.Number } field
     * @param { number } newValue
     */
    onMaxFileChange: function (field, newValue) {
        var me = this;
        me.record.set('maxFiles', newValue);
    }
});
// {/block}
