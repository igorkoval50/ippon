;(function ($, window) {
    "use strict";

    /**
     * Overwrites swMenuScroller to round elWidth because of jQuery 3.3 Bugfix
     * ".width(), .height(), .css(“width”), and .css(“height”) to return decimal values
     *
     */
    $.overridePlugin('swMenuScroller', {

        /**
         * Updates the buttons status and toggles their visibility.
         *
         * @public
         * @method updateButtons
         */
        updateButtons: function () {
            var me = this,
                $list = me.$list,
                elWidth = Math.ceil(me.$el.width()),
                listWidth = $list.prop('scrollWidth'),
                scrollLeft = $list.scrollLeft();

            me.$leftArrow.toggle(scrollLeft > 0);
            me.$rightArrow.toggle(listWidth > elWidth && scrollLeft < (listWidth - elWidth));

            $.publish('plugin/swMenuScroller/onUpdateButtons', [ me, me.$leftArrow, me.$rightArrow ]);
        },

    });
})(jQuery, window);