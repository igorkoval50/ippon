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
 * @package    Blog
 * @subpackage Controller
 * @version    $Id$
 * @author shopware AG
 */

/**
 * Shopware Controller - Blog backend module
 *
 * Detail controller of the blog module. Handles all action around to
 * edit or create and list a blog.
 */
//{namespace name=backend/blog/view/blog}
//{block name="backend/blog/controller/blog" append}
Ext.define('Mbdus.apps.Blog.controller.Blog', {
    /**
     * override
     * @string
     */
    override:'Shopware.apps.Blog.controller.Blog',

    /**
     * Event listener method which will be fired when the user
     * clicks the "save"-button in the edit-window.
     *
     * @event click
     * @param [object] btn - pressed Ext.button.Button
     * @return void
     */
    onSaveBlogArticle: function (btn) {
        var me = this,
            formPanel = me.getDetailWindow().formPanel,
            form = formPanel.getForm(),
            listStore = me.subApplication.listStore,
            record = form.getRecord();
       
        me.callParent(arguments);
        
        var values = form.getFieldValues();
   
        Ext.Ajax.request({
            method: 'POST',
            url: '{url controller=AttributeData action=saveData}',
            params: {
                _foreignKey: record.get('id'),
                _table: 's_blog_attributes',
                __attribute_mbdus_seourl: values.mbdusSeoUrl
            }
        });
    }
});
//{/block}
