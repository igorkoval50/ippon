;(function($) {
    'use strict';

    $.plugin('swagCustomProductsNumberfield', {

        /** @object Default plugin configuration */
        defaults: {

            /** @array Array of white listed key codes from 0 to 9 including comma, period, backspace, delete and substraction signs */
            whitelistKeys: [ 48, 96, 49, 97, 50, 98, 51, 99, 52, 100, 53, 101, 54, 102, 55, 103, 56, 104, 57, 105, 188, 108, 190, 8, 46, 109, 189 ]
        },

        /**
         * Initializes the plugin and applies the necessary event listeners.
         *
         * @returns void
         */
        init: function() {
            var me = this;
            me._on(me.$el, 'keydown', $.proxy(me.onKeyPress, me));

            $.publish('plugin/swagCustomProductsNumberfield/init', [ me ]);
        },

        /**
         * Event listener which will be fired when the user presses a button when the element is focused.
         *
         * The method checks if the entered keycode is in our blacklist {@link blockedKeys}
         *
         * @param {jQuery.Event} e
         * @returns {boolean}
         */
        onKeyPress: function(e) {
            var me = this,
                key = e.keyCode || e.which;

            if (me.opts.whitelistKeys.indexOf(key) === -1) {
                e.preventDefault();

                $.publish('plugin/swagCustomProductsNumberfield/onKeyPressBlockedKey', [ me, key ]);
                return false;
            }
        },

        /**
         * Destroy method of the plugin.
         * Removes attached event listener.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me._off(me.$el, 'keydown');

            this._destroy();
        }
    });

    $(function() {
        StateManager.addPlugin('*[data-custom-products-number-field="true"]', 'swagCustomProductsNumberfield');
    });
})(jQuery);
