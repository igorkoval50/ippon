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
 * @category    Shopware
 * @package     Emotion
 * @subpackage  View
 * @version     $Id$
 * @author      shopware AG
 */

//{namespace name=backend/emotion/view/detail}
//{block name="backend/emotion/controller/detail" append}
Ext.define('Mbdus.apps.Emotion.controller.Detail', {

    override: 'Shopware.apps.Emotion.controller.Detail',

    /**
     * Event will be fired when the user want to save the current emotion of the detail window
     * @param record
     * @param preview
     */
    onSaveEmotion: function(record, preview) {
        var me = this,
            settings = me.getSettingsForm(),
            attributeForm = me.getAttributeForm(),
            sidebar = me.getSidebar(),
            layout = me.getLayoutForm(),
            win = me.getDetailWindow(),
            activeTab = win.sidebar.items.indexOf(win.sidebar.getActiveTab());

        me.callParent(arguments);
        
        Ext.Ajax.request({
            method: 'POST',
            url: '{url controller=AttributeData action=saveData}',
            params: {
                _foreignKey: record.get('id'),
                _table: 's_emotion_attributes',
                __attribute_mbdus_seourl: settings.getForm().getValues().mbdusSeoUrl
            }
        });

        return true;
    }
});
//{/block}
