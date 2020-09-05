// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/detail/synonym_group/synonyms"}
Ext.define('Shopware.apps.SwagFuzzy.view.detail.synonymGroup.Synonyms', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.swagFuzzy-synonyms-grid',
    title: '{s name=synonyms/title}Synonyms{/s}',
    height: 300,

    configure: function () {
        return {
            rowEditing: true,
            pagingbar: false,
            columns: {
                name: {
                    header: '{s name=synonyms/nameColumn}Synonym{/s}'
                }
            }
        };
    },

    createDeleteColumn: function () {
        var me = this;
        var column = me.callParent(arguments);

        column.tooltip = '{s name=synonyms/actionColumn/deleteSynonym}Delete synonym{/s}';

        return column;
    },

    createEditColumn: function () {
        var me = this;
        var column = me.callParent(arguments);

        column.handler = function (view, rowIndex, colIndex, item, opts, record) {
            me.rowEditor.startEdit(record, 1);
        };
        column.tooltip = '{s name=synonyms/actionColumn/editSynonym}Edit synonym{/s}';

        return column;
    },

    createAddButton: function () {
        var me = this;

        var button = me.callParent(arguments);

        button.handler = function () {
            var record = Ext.create('Shopware.apps.SwagFuzzy.model.Synonyms', {});
            me.getStore().add(record);
            me.rowEditor.startEdit(record, 1);
        };

        return button;
    }
});
// {/block}
