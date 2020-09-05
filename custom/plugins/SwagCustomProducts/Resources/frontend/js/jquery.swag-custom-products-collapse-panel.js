;(function($, window) {
    'use strict';

    /**
     * Small helper plugin which toggles panels.
     */
    $.plugin('swagCustomProductsCollapsePanel', {

        /** @object Default plugin configuration */
        defaults: {

            /** @string Parent container selector */
            parentWrapperSelector: '.custom-products--option',

            /** @string Panel selector */
            customProductWrapperSelector: '.custom-product--option-wrapper',

            /** @string Active class */
            activeCls: 'is--active',

            /** @string Sliding speed in milli seconds */
            slideToggleSpeed: 175
        },

        /**
         * Initializes the plugin and applies the necessary event listeners.
         *
         * @returns void
         */
        init: function() {
            var me = this;

            me._on(me.$el, 'click', $.proxy(me.onTogglePanel, me));

            me.displayRequiredPanels();

            $.publish('plugin/swagCustomProductsCollapsePanel/init', [ me ]);
        },

        /**
         * Searches for active panels and shows them on start up.
         *
         * We have to use jQuery to show / hide the boxes cause `display: block` would
         * destroy the panel animations.
         *
         * @returns void
         */
        displayRequiredPanels: function() {
            var me = this;

            me.$el.parents(me.opts.parentWrapperSelector).each(function(i, item) {
                var $parent = $(item),
                    $panel = $parent.find(me.opts.customProductWrapperSelector);

                if (!$parent.hasClass(me.opts.activeCls)) {
                    return;
                }

                $panel.show();
            });

            $.publish('plugin/swagCustomProductsCollapsePanel/displayRequiredPanels', [ me ]);
        },

        /**
         * Event listener method which will be fired when the user clicks `me.$el`.
         *
         * The method selects the parent DOM node of the clicked element and toggles
         * panel DOM node.
         *
         * @param e {jQuery.Event}
         */
        onTogglePanel: function(e) {
            var me = this,
                $target = $(e.target),
                $parent = $target.parents(me.opts.parentWrapperSelector),
                $panel = $parent.find(me.opts.customProductWrapperSelector);

            e.preventDefault();

            if ($parent.hasClass(me.opts.activeCls)) {
                $parent.removeClass(me.opts.activeCls);
                $panel.stop().slideUp(me.opts.slideToggleSpeed);
            } else {
                $parent.addClass(me.opts.activeCls);
                $panel.stop().slideDown(me.opts.slideToggleSpeed);
            }

            $.publish('plugin/swagCustomProductsCollapsePanel/onTogglePanel', [ me, $panel, $parent, $target ]);
        },

        /**
         * Destroy method of the plugin.
         * Removes attached event listener.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me._off(me.$el, 'click');

            me._destroy();
        }
    });

    $(function() {
        StateManager.addPlugin('*[data-custom-products-collapse-panel="true"]', 'swagCustomProductsCollapsePanel');
    });
})(jQuery, window);
