/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   NetiFoundation
 * @author     bmueller
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.PagingComboBox', {
    'extend': 'Shopware.form.field.PagingComboBox',
    'alias': 'widget.netipagingcombobox',
    'disableLoadingSelectedName': true,

    'initComponent': function () {
        var me = this;

        me.callParent(arguments);

        me.addListener(
            'afterrender',
            function () {
                me.store.load({
                    'params': {
                        'id': me.getValue()
                    },
                    'callback': function () {
                        var record = me.store.getById(me.getValue());
                        me.select(record);
                    }
                });
            }
        );

        me.on('expand', Ext.bind(me.loadStore, me));
        me.on('blur', Ext.bind(me.checkValue, me));
    },
    'loadStore': function () {
        var me = this;

        me.store.load({
            'callback': function () {
                var record = me.store.getById(me.getValue());
                me.select(record);
            }
        });
    },
    'checkValue': function () {
        var me = this,
            value = me.getValue(),
            fieldValue = me.inputEl.getValue();

        if (value === null || fieldValue === null || !fieldValue.length) {
            me.clearValue();
        }
    }
});
//{/block}
