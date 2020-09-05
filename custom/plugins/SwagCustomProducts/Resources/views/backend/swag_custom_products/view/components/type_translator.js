// {namespace name="backend/swag_custom_products/option/detail"}
// {block name="backend/swag_custom_products/components/TypeTranslator"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.TypeTranslator', {

    snippets: {
        types: {
            // {block name="backend/swag_custom_products/components/typeTranslator/snippets"}{/block}
            checkbox: '{s name="combo/value/name/checkbox"}Checkbox{/s}',
            multiselect: '{s name="combo/value/name/multiselect"}Multiselect{/s}',
            numberfield: '{s name="combo/value/name/numberfield"}Numberfield{/s}',
            radio: '{s name="combo/value/name/radio"}RadioSelect{/s}',
            select: '{s name="combo/value/name/select"}Combobox{/s}',
            textarea: '{s name="combo/value/name/textarea"}Textarea{/s}',
            textfield: '{s name="combo/value/name/textfield"}Text{/s}',
            colorselect: '{s name="combo/value/name/colorselection"}Color selection{/s}',
            imageselect: '{s name="combo/value/name/imageselection"}Image selection{/s}',
            fileupload: '{s name="combo/value/name/fileupload"}File upload{/s}',
            imageupload: '{s name="combo/value/name/imageupload"}Image upload{/s}',
            date: '{s name="combo/value/name/date"}Date field{/s}',
            time: '{s name="combo/value/name/time"}Time field{/s}',
            wysiwyg: '{s name="combo/value/name/wysiwyg"}HTML editor{/s}'
        }
    },

    /**
     * @param value
     * @returns { * }
     */
    getTranslation: function (value) {
        var me = this;

        return me.snippets.types[value];
    }
});
// {/block}
