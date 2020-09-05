// {block name="backend/swag_custom_products/components/rowTextEditor"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.RowTextEditor', {
    extend: 'Ext.form.field.Text',

    allowBlank: false,

    /**
     * Simple validation for the valueString.
     * Filter all starting whitespaces and check only for a empty string
     *
     * @param { string } value
     * @return boolean
     **/
    validateValue: function (value) {
        var startingWhiteSpace = /^\s*/;

        value = value.replace(startingWhiteSpace, '');

        return value.length > 0;
    }
});
// {/block}
