/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Supplier
 * @subpackage View
 * @version    $Id$
 * @author shopware AG
 */

/*{namespace name=backend/supplier/view/edit}*/

/**
 * Shopware View - Supplier
 *
 * Backend - Management for Suppliers. Create | Modify | Delete and Logo Management.
 * Create a edit supplier view
 */
//{block name="backend/supplier/view/main/edit" append}
Ext.define('Mbdus.apps.Supplier.view.main.Edit', {
    override : 'Shopware.apps.Supplier.view.main.Edit',

    /**
     * Return the entire form
     *
     *  @return Ext.form.Panel
     */
    getInfoForm : function()
    {
        var me = this,
            logoArray = [],
            elements = me.callParent(arguments), 
            fieldset;
      
        fieldset = elements.items.items[1];
        
        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
            labelWidth: 155,
		    translatable: true,
		    translationName: 'mbdusSeoUrl',
		    helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, über die der Hersteller aufrufbar sein soll.{/s}'
        });
        
        fieldset.insert(0, me.mbdusSeoUrl);
        elements.items.items[1]=fieldset;
      
        me.mbdusSeoUrl.setValue(me.record.raw.attribute.mbdusSeoUrl);
        
        return elements;
    }
});
//{/block}
