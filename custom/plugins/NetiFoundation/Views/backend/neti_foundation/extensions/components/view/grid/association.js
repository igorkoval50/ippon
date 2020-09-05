/**
 *
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.components.view.grid.Association', {
    'extend': 'Shopware.grid.Association',
    'searchComboDisplayField': 'name',
    'searchComboValueField': 'id',
    'createSearchCombo': function (store) {
        var me = this;

        return Ext.create('Shopware.form.field.Search', {
            'name': 'associationSearchField',
            'displayField': me.searchComboDisplayField,
            'valueField': me.searchComboValueField,
            'store': store,
            'pageSize': 20,
            'flex': 1,
            'subApp': me.subApp,
            'fieldLabel': me.searchComboLabel,
            'margin': 5,
            'listeners': {
                'select': function (combo, records) {
                    me.onSelectSearchItem(combo, records);
                }
            }
        });
    },

    'createDeleteColumn': function () {
        var me = this, column;

        column = {
            'action': 'delete',
            'iconCls': 'sprite-minus-circle-frame',
            'handler': function (view, rowIndex, colIndex, item, opts, record) {
                me.onRemoveAssignedAssociation(record);
            }
        };

        me.fireEvent(me.eventAlias + '-delete-action-column-created', me, column);

        return column;
    },

    'onRemoveAssignedAssociation': function (record) {
        var me = this,
            store = me.getStore(),
            title = '{s namespace="backend/application/main" name="grid_controller/delete_confirm_title"}Delete items{/s}',
            text = '{s namespace="backend/application/main"  name="grid_controller/delete_confirm_text"}Are you sure you want to delete the selected items?{/s}';

        if (record instanceof Ext.data.Model) {
            Ext.MessageBox.confirm(title, text, function (response) {
                if (response !== 'yes') {
                    return false;
                }

                store.remove(record);
            });
        }
    }
});
//{/block}
