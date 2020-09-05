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
 * Shopware UI - Site main Controller
 *
 * This file handles the navigation tree containing the actual sites.
 */

//{namespace name=backend/site/site}

//{block name="backend/site/controller/tree" append}
Ext.define('Mbdus.apps.Site.controller.Tree', {

    override: 'Shopware.apps.Site.controller.Tree',

    /**
     * Event listener method which is called when an item in the tree is clicked.
     * Depending on the type (leaf or non-leaf), it will activate the necessary buttons for user interaction
     * Further, if the item is a Site, it will generate an array from the grouping string, which is necessary for the checkboxes to work correctly
     * It will then call form.loadRecord() to display the record in the detail form.
     * An embed code for the site will be created as well.
     * If the item is not a site, it will just set button states accordingly.
     *
     * @param item
     * @param record
     */
    onItemClick: function(item,record) {

        var me = this,
            form = me.getDetailForm(),
            translation = form.translationPlugin,
            /*{if {acl_is_allowed privilege=deleteGroup}}*/
            deleteGroupButton = me.getDeleteGroupButton(),
            /*{/if}*/
            /*{if {acl_is_allowed privilege=deleteSite}}*/
            deleteSiteButton = me.getDeleteSiteButton(),
            /*{/if}*/
            saveSiteButton = me.getSaveSiteButton(),

			ddselector = form.down('ddselector'),
			groupStore = ddselector.fromStore,
			selectedStore = ddselector.toStore;

        me.getAttributeForm().setDisabled(true);
        
        translation.translationMerge = false;
        translation.translationKey = record.get('helperId');
        translation.initConfig(form);

        //determine if the item is a group or a site
        if (record.data.parentId != 'root' || record.isLeaf()) {

            //set button states
            /*{if {acl_is_allowed privilege=deleteGroup}}*/
            deleteGroupButton.disable();
            /*{/if}*/
            /*{if {acl_is_allowed privilege=deleteSite}}*/
            deleteSiteButton.enable();
            /*{/if}*/
            /*{if {acl_is_allowed privilege=updateSite}}*/
            form.saveButton.enable();
            /*{else}*/
            form.saveButton.disable();
            /*{/if}*/

			groupStore.load({
				params: {
					grouping: record.data.grouping
				}
			});
			selectedStore.load({
				params: {
					grouping: record.data.grouping
				}
			});
            //load record into the form
            //hotfix find a better solution for this after beta
            //record.data.description = record.data.description.split("(")[0];
            form.loadRecord(record);
            me.getAttributeForm().loadAttribute(record.get('helperId'));
   
            if(record.raw.attribute){
            	form.getForm().findField('mbdusSeoUrl').setValue(record.raw.attribute.mbdusSeoUrl);
            }
            else{
            	form.getForm().findField('mbdusSeoUrl').setValue('');
            }

            //build and set the embed code
            //the preceding '<' is necessary to display the string without interference from the script renderer
            var embedCode = '<' + 'a href="{literal}{url controller=custom sCustom={/literal}' + record.data.helperId + '}" title="' + record.data.description + '">' + record.data.description +'</a>';
            form.down('textfield[name=embedCode]').setValue(embedCode);

        } else {
            //set button states
            /*{if {acl_is_allowed privilege=deleteGroup}}*/
            deleteGroupButton.enable();
            /*{/if}*/
            /*{if {acl_is_allowed privilege=deleteSite}}*/
            deleteSiteButton.disable();
            /*{/if}*/
        }

    }
});
//{/block}
