/**
 *
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.components.controller.Controller', {
    'extend': 'Enlight.app.Controller',

    'mixins': {
        'helper': 'Shopware.model.Helper'
    },

    'buttonHandler': function (record, className, events) {
        var me = this,
            window;

        if (!(record instanceof Ext.data.Model)) {
            return false;
        }

        if (me.hasModelAction(record, 'detail')) {
            record.reload({
                callback: function (result) {
                    window = me.createDetailWindow(
                        result,
                        className
                    );

                    Ext.Object.each(events, function (key, value) {
                        Shopware.app.Application.on(
                            window.eventAlias + '-' + key,
                            value,
                            me,
                            {
                                'single': true
                            }
                        );
                    });

                    // Shopware.app.Application.fireEvent(me.getEventName('after-edit-item'), me, window, listing, record);
                }
            });

            return true;
        } else {
            me.createDetailWindow(
                record,
                className
            );

            return true;
        }
    },

    'createDetailWindow': function (record, className) {
        var me = this,
            window;

        window = me.subApplication.getView(className).create({
            'record': record
        });

        return window;
    }
});
//{/block}
