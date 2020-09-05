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
 * @package    Category
 * @subpackage Main
 * @version    $Id$
 * @author shopware AG
 */

/* {namespace name=backend/category/main} */

/**
 * Shopware Controller - category management controller
 *
 * The category management controller handles the initialisation of the mainWindow and takes care
 * of most of the communication to and from the server.
 */
//{block name="backend/category/controller/main" append}
Ext.define('Mbdus.apps.Category.controller.Main', {
    /**
     * override
     * @string
     */
    override: 'Shopware.apps.Category.controller.Main',

    /**
     * Event listener method which will be fired when the user
     * clicks the "save"-button in every window.
     *
     * @param [object] btn - pressed Ext.button.Button
     * @event click
     * @return void
     */
    onSaveSettings: function (button, event) {
        var me = this,
        window = me.getMainWindow(),
        form = window.formPanel.getForm(),
        categoryModel = form.getRecord(),
        selectedNode = me.getController("Tree").getSelectedNode(),
        parentNode = selectedNode.parentNode || selectedNode;

        me.callParent(arguments);

        var values = form.getFieldValues();

        Ext.Ajax.request({
        	method: 'POST',
        	url: '{url controller=AttributeData action=saveData}',
        	params: {
        		_foreignKey: categoryModel.get('id'),
        		_table: 's_categories_attributes',
        		__attribute_mbdus_seourl: values.mbdusSeoUrl
        	}
        });
    }
});
//{/block}
