/**
 * Shopware UI - Site site Form View
 *
 * This file contains the layout of the modules detail form.
 */

//{namespace name=backend/site/site}

//{block name="backend/site/view/site/form" append}
Ext.define('Mbdus.apps.Site.view.site.Form', {
	override: 'Shopware.apps.Site.view.site.Form',

    getOptionsField: function() {
        var me = this, elements = me.callParent(arguments);
        
        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
            anchor:'100%',
            labelWidth: 155,
            translatable: true,
            helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, über die die Seite aufrufbar sein soll.{/s}'
        });
       
        elements = Ext.Array.insert(elements, 8, [me.mbdusSeoUrl]);

        return elements;
    }
});
//{/block}



