// {block name="backend/config/custom_products_facet"}
Ext.define('Shopware.apps.Config.CustomProductsFacet', {

    getClass: function() {
        return 'SwagCustomProducts\\Bundle\\SearchBundle\\Facet\\CustomProductsFacet';
    },

    createItems: function () {
        return [{
            xtype: 'textfield',
            name: 'label',
            labelWidth: 150,
            translatable: true,
            fieldLabel: '{s name="facet_label"}{/s}'
        }];
    }
});
// {/block}
