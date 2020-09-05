// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/main/profiles"}
Ext.define('Shopware.apps.SwagFuzzy.view.main.Profiles', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.swagFuzzy-main-profiles',

    store: Ext.create('Shopware.apps.SwagFuzzy.store.Profiles'),

    configure: function () {
        return {
            detailWindow: 'Shopware.apps.SwagFuzzy.view.detail.profile.Window',
            columns: {
                name: { header: '{s name=profiles/profileNameColumn}Profile name{/s}' },
                standard: {
                    header: '{s name=profiles/standardColumn}Standard{/s}',
                    flex: 0.5
                }
            }
        };
    },

    createActionColumnItems: function () {
        var me = this,
            items;

        items = me.callParent(arguments);
        items.push(
            {
                action: 'applyProfile',
                iconCls: 'sprite-application-home',
                tooltip: '{s name=profiles/actionColumn/applyProfile}Apply profile{/s}',
                handler: function (view, rowIndex, colIndex, item, opts, record) {
                    me.fireEvent('apply-profile', me, view, record);
                }
            }
        );

        return items;
    },

    createDeleteColumn: function () {
        var me = this,
            column = me.callParent(arguments);

        column.tooltip = '{s name=profiles/actionColumn/deleteSynonym}Delete profile{/s}';

        return column;
    },

    createEditColumn: function () {
        var me = this,
            column = me.callParent(arguments);

        column.tooltip = '{s name=profiles/actionColumn/editSynonym}Edit profile{/s}';

        return column;
    },

    createAddButton: function () {
        var me = this,
            button = me.callParent(arguments);

        button.tooltip = '{s name=profiles/addButton/tooltip}Adds a new profile and saves the current settings{/s}';

        return button;
    }
});
// {/block}
