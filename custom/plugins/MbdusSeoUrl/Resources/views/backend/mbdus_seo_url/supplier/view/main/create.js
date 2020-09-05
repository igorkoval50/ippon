/*{namespace name=backend/supplier/view/create}*/

/**
 * Shopware View - Supplier
 *
 * Backend - Management for Suppliers. Create | Modify | Delete and Logo Management.
 * Create a new supplier view
 */
//{block name="backend/supplier/view/main/create" append}
Ext.define('Mbdus.apps.Supplier.view.main.Create', {
    override : 'Shopware.apps.Supplier.view.main.Create',
    
    /**
     * Returns the whole form to edit the supplier
     *
     * @returns Ext.form Panel
     */
    getFormPanel : function()
    {
        var me = this, elements = me.callParent(arguments), fieldset;

        fieldset = elements.items.items[1];

        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
		    translatable: true,
		    labelWidth: 155,
		    translationName: 'mbdusSeoUrl',
		    helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, &uuml;ber die der Hersteller aufrufbar sein soll.{/s}'
        });
        
        fieldset.insert(0, me.mbdusSeoUrl);
        elements.items.items[1]=fieldset;
        
        return elements;
    },
});
//{/block}
