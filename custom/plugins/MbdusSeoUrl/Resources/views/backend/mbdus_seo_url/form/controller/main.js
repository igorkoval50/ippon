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
 * @package    Form
 * @subpackage Controller
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/form/controller/main}

/**
 * todo@all: Documentation
 */
//{block name="backend/form/controller/main" append}
Ext.define('Mbdus.apps.Form.controller.Main', {

    override: 'Shopware.apps.Form.controller.Main',

    /**
     * Function to save a form
     *
     * @event click
     * @param [object] btn Contains the clicked button
     * @return void
     */
    onSaveForm: function(btn) {
        var me         = this,
            win        = btn.up('window'),
            formPanel  = win.down('form'),
            form       = formPanel.getForm(),
            record     = form.getRecord(),
            fieldStore = me.getStore('Field');

       me.callParent(arguments);
       
       var values = form.getFieldValues();
        
        Ext.Ajax.request({
            method: 'POST',
            url: '{url controller=AttributeData action=saveData}',
            params: {
                _foreignKey: record.data.id,
                _table: 's_cms_support_attributes',
                __attribute_mbdus_seourl: values.mbdusSeoUrl
            }
        });
    }
});
//{/block}
