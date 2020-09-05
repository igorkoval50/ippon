;(function($) {
    'use strict';

    /**
     * the description Plugin...
     * open a modal with the description of a custom_product "Option".
     */
    $.plugin('swagCustomProductsDescription', {
        defaults: {
            /**
             * attribute for the ModalTitle
             */
            title: '',

            /**
             * the the data-attribute name with the selector for the
             * content div
             */
            contentGetter: 'data-content-selector'
        },

        /**
         * init the plugin
         */
        init: function () {
            var me = this;

            me.applyDataAttributes();

            me.subscribeEvents();
        },

        /**
         * read the content string from the assigned div container
         *
         * @returns {string}
         */
        getContent: function () {
            var me = this,
                selector = me.$el.attr(me.opts.contentGetter),
                content = $('.' + selector).html();

            return [
                '<div class="custom-product--modal-content">',
                content,
                '</div>'
            ].join('');
        },

        /**
         * subscribe the events
         */
        subscribeEvents: function () {
            var me = this;

            me.$el.on('click', $.proxy(me.onClick, me));
        },

        /**
         * Open a modal with content and title.
         */
        onClick: function () {
            var me = this;

            $.modal.open(me.getContent(), { title: me.opts.title });
        },

        /**
         * Destroy method of the plugin.
         * Removes attached event listener.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me.$el.on('click');

            me._destroy();
        }
    });

    // Add the Plugin to the "StateManager"
    $(function() {
        StateManager.addPlugin('*[data-description-plugin="true"]', 'swagCustomProductsDescription');
    });
})(jQuery);
