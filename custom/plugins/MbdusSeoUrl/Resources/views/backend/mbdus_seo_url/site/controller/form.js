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
 * @package    Site
 * @subpackage Controller
 * @version    $Id$
 * @author shopware AG
 */

/**
 * Shopware UI - Site form Controller
 *
 * This file handles creation and saving of the detail form.
 */

//{namespace name=backend/site/site}

//{block name="backend/site/controller/form" append}
Ext.define('Mbdus.apps.Site.controller.Form', {

    override: 'Shopware.apps.Site.controller.Form',

    /**
     * Event listener method which is called when the onSaveSite event was fired.
     * It'll get all of the forms values and then call model.save().
     */
    onSaveSite: function() {
        var me = this,
            form = me.getDetailForm(),
			ddselector = form.down('ddselector'),
			toStore = ddselector.toStore,
            values = form.getValues(),
			record = form.getRecord(),
            model;

        me.callParent(arguments);
       
        Ext.Ajax.request({
            method: 'POST',
            url: '{url controller=AttributeData action=saveData}',
            params: {
                _foreignKey: record.data.helperId,
                _table: 's_cms_static_attributes',
                __attribute_mbdus_seourl: values.mbdusSeoUrl
            }
        });
    }
});
//{/block}
