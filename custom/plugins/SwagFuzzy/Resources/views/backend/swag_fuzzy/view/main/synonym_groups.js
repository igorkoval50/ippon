// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/main/synonym_groups"}
Ext.define('Shopware.apps.SwagFuzzy.view.main.SynonymGroups', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.swagFuzzy-main-synonymGroups',

    store: Ext.create('Shopware.apps.SwagFuzzy.store.SynonymGroups'),

    configure: function () {
        return {
            detailWindow: 'Shopware.apps.SwagFuzzy.view.detail.synonymGroup.Window',
            columns: {
                groupName: { header: '{s name=synonymGroups/groupNameColumn}Group name{/s}' },
                active: { header: '{s name=synonymGroups/activeColumn}Active{/s}' }
            }
        };
    },

    createActionColumnItems: function () {
        var me = this,
            items;
        items = me.callParent(arguments);
        items.push(
            {
                action: 'duplicateSynonymGroup',
                iconCls: 'sprite-duplicate-article',
                tooltip: '{s name=synonymGroups/actionColumn/duplicateSynonymGroup}duplicate synonym group{/s}',
                handler: function (view, rowIndex, colIndex, item, opts, record) {
                    me.fireEvent('duplicate-synonymGroup-item', me, view, record);
                }
            }
        );

        return items;
    },

    createDeleteColumn: function () {
        var me = this,
            column;

        column = me.callParent(arguments);
        column.tooltip = '{s name=synonymGroups/actionColumn/deleteSynonymGroup}delete synonym group{/s}';

        return column;
    },

    createEditColumn: function () {
        var me = this,
            column;

        column = me.callParent(arguments);
        column.tooltip = '{s name=synonymGroups/actionColumn/editSynonymGroup}edit synonym group{/s}';

        return column;
    }
});
// {/block}
