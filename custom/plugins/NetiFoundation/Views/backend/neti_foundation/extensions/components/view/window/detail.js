/**
 *
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.components.view.window.Detail', {
    'extend': 'Shopware.window.Detail',
    'border': null,
    'autoShow': true,
    'associationComponentBaseType': 'detail',

    'initComponent': function () {
        var me = this,
            desktop = Shopware.app.Application.viewport.getActiveDesktop();

        if (me.width >= desktop.getWidth()) {
            me.width = desktop.getWidth() * 0.8;
        }

        if (me.height >= desktop.getHeight()) {
            me.height = desktop.getHeight() * 0.8;
        }
        me.callParent(arguments);
    },

    'createTabItem': function (association) {
        var me = this, item;

        if (!me.fireEvent(me.getEventName('before-create-tab-item'), me, association)) {
            return false;
        }

        if (association.isBaseRecord) {
            item = me.createAssociationComponent(me.associationComponentBaseType, me.record, null, null, me.record);
        } else {
            item = me.createAssociationComponent(
                me.getComponentTypeOfAssociation(association),
                Ext.create(association.associatedName),
                me.getAssociationStore(me.record, association),
                association,
                me.record
            );
        }
        me.associationComponents[association.associationKey] = item;

        me.fireEvent(me.getEventName('after-create-tab-item'), me, association, item);

        if (item.title === undefined) {
            item.title = me.getModelName(association.associatedName);
        }

        return item;
    }
});
//{/block}
