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
 * @package    Article
 * @subpackage Main
 * @version    $Id$
 * @author shopware AG
 */

/**
 * Shopware Controller - Article backend module
 */
//{namespace name=backend/article/view/main}
//{block name="backend/article/controller/main" append}
Ext.define('Mbdus.apps.Article.controller.Main', {

    /**
     * The parent class that this class extends.
     * @string
     */
    override:'Shopware.apps.Article.controller.Main',

    /**
     * Event listener method which will be triggered when the user changes the selected product in
     * the product list module.
     *
     * The method checks if the currently openend instance of the product mask is running in split view mode
     * and reloads the detail store of the selected product
     *
     * @param { Enlight.app.SubApplication } subApp - Sub application which triggers the split view, usally
     *        the product list module
     * @param { Array } options - Passed options
     * @returns { Boolean }
     */
    onSplitViewStoreChange: function(subApp, options) {
        var me = this,
            mainWindow = me.mainWindow,
            form = mainWindow.detailForm;
        
        me.callParent(arguments);
        me.detailStore = me.getStore('Detail');       
        me.detailStore.load({
          callback: function(records) {
              var article = records[0];
              if(article.getMainDetailStore.data.items[0].raw.attribute.mbdusSeourl){
            	  form.getForm().findField("mbdusSeoUrl").setValue(article.getMainDetailStore.data.items[0].raw.attribute.mbdusSeourl);
              }
              else{
            	  form.getForm().findField("mbdusSeoUrl").setValue('');
              }
          }
        });
    }
});
//{/block}
