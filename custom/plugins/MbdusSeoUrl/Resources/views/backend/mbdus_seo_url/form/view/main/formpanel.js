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
 * @subpackage Main
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/form/view/main}

/**
 * todo@all: Documentation
 */
//{block name="backend/form/view/main/formpanel" append}
Ext.define('Mbdus.apps.Form.view.main.Formpanel', {
    override  : 'Shopware.apps.Form.view.main.Formpanel',
    
    /**
     * Sets up the ui component
     *
     * @return void
     */
    initComponent: function() {
        var me = this;

        me.items = me.getItems();
        me.dockedItems = me.getButtons();

        me.callParent(arguments);

        if (me.record !== undefined) {
            me.loadRecord(me.record);
            me.getForm().findField('mbdusSeoUrl').setValue(me.record.raw.attribute.mbdusSeoUrl);
        }
    },

    /**
     * Creates items shown in form panel
     *
     * @return array
     */
    getItems: function() {
        var me = this, elements = me.callParent(arguments);
        
        me.mbdusSeoUrl = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'mbdusSeoUrl',
            fieldLabel:'{s name=seourl}SEO-Url{/s}',
            labelWidth: 155,
            translatable: true,
            helpText: '{s name=seourl/helptext}Geben Sie hier die Url ein, &uuml;ber die die Seite aufrufbar sein soll.{/s}'
        });

        elements = Ext.Array.insert(elements, 7, [me.mbdusSeoUrl]);

        return elements;
    }
});
//{/block}
