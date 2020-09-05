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
//{block name="backend/emotion/view/detail/settings" append}
Ext.define('Mbdus.apps.Emotion.view.detail.Settings', {

    override: 'Shopware.apps.Emotion.view.detail.Settings',

    createLandingPageFieldset: function() {
        var me = this, fieldset, store;
        
        fieldset = me.callParent(arguments);
        
        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
            labelWidth: me.defaults.labelWidth,
            translatable: true,
            helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, über die die Landingpage aufrufbar sein soll.{/s}'
        });

        fieldset.add(me.mbdusSeoUrl);
        
        if(me.emotion.raw.attribute){
        	me.mbdusSeoUrl.setValue(me.emotion.raw.attribute.mbdusSeoUrl);
        }
     
        return fieldset;
    }
});
//{/block}
