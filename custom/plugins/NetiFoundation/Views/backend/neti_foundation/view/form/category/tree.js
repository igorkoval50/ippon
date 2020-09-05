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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.category.Tree
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/category"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.category.Tree', {
    'extend': 'Ext.tree.Panel',
    'requires': [],

    'alias': 'widget.neti_contacts-category-tree',
    'cls': Ext.baseCSSPrefix + 'category-tree',
    'snippets': {
        'title': '{s name=category-tree-title}Category{/s}',
        'tooltip': '{s name=category-tree-tooltip}Add category{/s}'
    },
    'multiSelect': true,
    'rootVisible': false,
    'addOnlyLeaf': false,

    'initComponent': function () {
        var me = this;

        me.columns = me.getColumns();

        me.callParent(arguments);
    },

    'getColumns': function () {
        var me = this;

        return [
            {
                'xtype': 'treecolumn',
                'text': '&nbsp;',
                'flex': 2,
                'sortable': true,
                'dataIndex': 'name'
            },
            {
                'xtype': 'actioncolumn',
                'width': 30,
                'items': [{
                    'iconCls': 'sprite-plus-circle-frame',
                    'tooltip': me.snippets.tooltip,
                    /**
                     * Handler for the action column
                     * @param view
                     * @param rowIndex
                     * @param colIndex
                     * @param item
                     */
                    'handler': function (view, rowIndex, colIndex, item, opts, record) {
                        var store = me.getGridField().getStore();

                        if (!store.findRecord('id', record.get('id'), 0, false, false, true)) {
                            store.add(record.getData());
                        }
                    },

                    'getClass': function (value, metadata, record) {
                        if (me.addOnlyLeaf && !record.isLeaf()) {
                            return 'x-hidden';
                        }
                    }
                }]
            }
        ];
    },

    'getGridField': function () {
        var me = this;

        return me.gridField;
    }
});
//{/block}
