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
 * @subpackage Controller
 * @version    $Id$
 * @author shopware AG
 */

/* {namespace name=backend/category/main} */

/**
 * Shopware Controller - category management controller
 *
 * The category management controller handles the initialisation of the category tree.
 */
//{block name="backend/category/controller/settings" append}
Ext.define('Mbdus.apps.Category.controller.Settings', {
    /**
     * override
     * @string
     */
    override: 'Shopware.apps.Category.controller.Settings',
    
    /**
     * Reacts if the event recordloaded is fired and hides or shows the template selection based
     * on the parent ID of the loaded record.
     *
     * @event recordloaded
     * @param record [Ext.data.Model]
     * @return void
     */
    onRecordLoaded : function(record, treeRecord) {
        var me = this,
            form = me.getSettingsForm(),
            store = form.templateComboBox.getStore(),
            records = store.getRange(),
            customTpl = record.get('template'),
            i = 0,
            count = records.length;
        
        me.callParent(arguments);

        if(record.getId() != me.subApplication.defaultRootNodeId){
        	form.getForm().findField('mbdusSeoUrl').setValue(record.raw.attribute.mbdusSeoUrl);
        }
    }
});
//{/block}

