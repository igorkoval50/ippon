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
 * @subpackage Settings
 * @version    $Id$
 * @author shopware AG
 */

/* {namespace name=backend/category/main} */

/**
 * Shopware UI - Category Settings
 *
 * Shows all Category detail settings
 */
//{block name="backend/category/view/tabs/settings" append}
Ext.define('Mbdus.apps.Category.view.category.tabs.Settings', {
  
    override:'Shopware.apps.Category.view.category.tabs.Settings',

    /**
     * Builds and returns the meta data section.
     * Fields
     *  - Meta Descriptions
     *  - Meta Keywords
     *
     * @return Ext.form.FieldSet
     */
    getMetaInfo : function()
    {
        var me = this, store, elements = me.callParent(arguments);
        
        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
            labelWidth: 155,
            translatable: true,
            helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, &uuml;ber die die Kategorie aufrufbar sein soll.{/s}'
        });
        
        elements.add(me.mbdusSeoUrl);
    
        return elements;
    }
});
//{/block}
