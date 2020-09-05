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
 * @subpackage Controller
 * @version    $Id$
 * @author shopware AG
 */

/*{namespace name=backend/supplier/controller/main}*/

/**
 * Shopware Controller - Supplier
 *
 * Backend - Management for Suppliers. Create | Modify | Delete and Logo Management.
 * Default supplier view. Extends a grid view.
 */
// {block name="backend/supplier/controller/main" append}
Ext.define('Mbdus.apps.Supplier.controller.Main', {
    
    override : 'Shopware.apps.Supplier.controller.Main',

    /**
     * This method will be called if the user hits the save button either in the edit window or
     * in the add supplier window
     *
     * @param btn Ext.button.Button
     * @return void
     */
    onSupplierSave: function(btn) {
        var win     = btn.up('window'), // Get Window
            form    = win.down('form'), // Get the DOM Form used in that window
            formBasis = form.getForm(), // Extract the form from the DOM
            me      = this,             // copy the current scope to me, because the 'this' scope tends to change
            store   = me.getStore('Supplier'), // load the supplier store
            record  = form.getRecord(),   // retrieve the record
            detailViewData = me.getDetailView().dataView,   // Detail view
            detailView = me.getDetailView();                // Detail View manager

        	me.callParent(arguments);
            Ext.Ajax.request({
                method: 'POST',
                url: '{url controller=AttributeData action=saveData}',
                params: {
                    _foreignKey: record.get('id'),
                    _table: 's_articles_supplier_attributes',
                    __attribute_mbdus_seourl: form.getForm().findField("mbdusSeoUrl").getValue()
                }
            });
    }
});
//{/block}
