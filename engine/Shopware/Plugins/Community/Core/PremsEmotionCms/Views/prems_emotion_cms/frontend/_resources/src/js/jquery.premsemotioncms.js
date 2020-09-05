;(function($, window) {
    "use strict";

    $.plugin('premsEmotionCms', {
        /**
         * Plugin default options.
         * Get merged automatically with the user configuration.
         */
        defaults: {
        },

        /**
         * Initializes the plugin, sets up event listeners and adds the necessary
         * classes to get the plugin up and running.
         *
         * @public
         * @method init
         */
        init: function () {
            var me = this,
                opts = me.opts,
                $el = me.$el;

            me.registerEvents();
        },

        /**
         * Registers all necessary event listeners for the plugin to proper operate.
         *
         * @public
         * @method registerEvents
         */
        registerEvents: function () {
            var me = this;
            var opts = me.opts;
            var $el = me.$el;

            //if ($(this).find('.emotion--wrapper.emotion--no-ajax')) {
                window.StateManager
                    .removePlugin('.emotion--wrapper', 'swEmotionLoader')
                    .addPlugin('.emotion--wrapper:not(.emotion--no-ajax)', 'swEmotionLoader')
                    .addPlugin('.emotion--no-ajax *[data-emotion="true"]', 'swEmotion');
            //}

            $.publish('plugin/premsEmotionCms/onRegisterEvents', me);
        },

        /**
         * Destroys the initialized plugin completely, so all event listeners will
         * be removed and the plugin data, which is stored in-memory referenced to
         * the DOM node.
         *
         * @public
         * @method destroy
         */
        destroy: function () {
            this._destroy();
        }
    });

    $(document).ready(function () {
        $(document).premsEmotionCms();
    })
}(jQuery, window));
