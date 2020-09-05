/**
 * Copyright notice
 *
 * (c) 2009-2016 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.category.Window
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/category"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.category.Window', {
    'extend': 'Enlight.app.Window',
    'alias': 'widget.neti_foundation-category-window',
    'border': false,
    'autoShow': true,
    'layout': 'fit',
    'width': 800,
    'height': 650,
    'maximizable': true,
    'minimizable': true,
    'stateful': true,
    'stateId': 'shopware-netifoundation-view-category',
    'title': '{s name="Window_Title_Category"}Kategorieliste{/s}',
    'addOnlyLeaf': false,
    /**
     *
     */
    'initComponent': function () {
        var me = this;

        me.items = [
            me.getPanel()
        ];

        me.dockedItems = [{
            dock: 'bottom',
            xtype: 'toolbar',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items: me.createFormButtons()
        }];

        me.callParent(arguments);
    },

    'getPanel': function() {
        var me = this;

        return Ext.create('Shopware.apps.NetiFoundation.view.form.category.Tree', {
            'store': Ext.create('Shopware.apps.Article.store.CategoryTree').load(),
            'gridField': me.gridField,
            'addOnlyLeaf': me.addOnlyLeaf
        });
    },

    'createFormButtons': function() {
        var me = this,
            items = ['->'];

        items.push({
            'text': '{s name="Close"}Close{/s}',
            'cls':'primary',
            'handler': function () {
                me.close();
            }
        });

        return items;
    }
});
//{/block}
