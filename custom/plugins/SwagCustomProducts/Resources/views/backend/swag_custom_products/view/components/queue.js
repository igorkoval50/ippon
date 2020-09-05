// {block name="backend/swag_custom_products/components/queue"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.Queue', {
    extend: 'Ext.Component',

    queue: null,

    /**
     * Init the Queue component
     */
    initComponent: function () {
        var me = this;

        me.initQueue();

        me.callParent(arguments);
    },

    /**
     * init the queue and set default values
     */
    initQueue: function () {
        var me = this;

        me.queue = [];
        me.startLength = 0;
        me.counter = 0;
    },

    /**
     * This method is to add values to the queue.
     * You can use a Array of objects or a object
     *
     * @param { object[] | object } value
     */
    enQueue: function (value) {
        var me = this;

        if (Ext.isArray(value)) {
            Ext.Array.each(value, function (item) {
                me.queue.push(item);
            });
        } else {
            me.queue.push(value);
        }

        me.startLength = me.queue.length;
    },

    /**
     * Return the next object in queue.
     * If no object left return false.
     *
     * @returns { object | bool }
     */
    deQueue: function () {
        var me = this,
            item;

        if (!me.queue.length) {
            return false;
        }

        item = me.queue.shift();

        me.counter++;

        return item;
    },

    /**
     * Return the length of the queue after the last enQueue method call.
     *
     * @returns { number }
     */
    getStartLength: function () {
        var me = this;

        return me.startLength;
    },

    /**
     * Return the count of the current deQueue calls
     *
     * @returns { number }
     */
    getCurrentStep: function () {
        var me = this;

        return me.counter;
    },

    /**
     *  Returns the decimal percentage of the enQueued items.
     *
     * @returns { number }
     */
    getPercentage: function () {
        var me = this;

        return (me.counter / me.startLength);
    }
});
// {/block}
