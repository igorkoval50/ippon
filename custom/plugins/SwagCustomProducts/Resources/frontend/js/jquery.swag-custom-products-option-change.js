;(function($, window) {
    'use strict';

    $.plugin('swagCustomProductsOptionChange', {

        /** @object Default plugin settings */
        defaults: {

            /** @string */
            fieldSelector: '*[data-field="true"]',

            /** @string */
            wysiwygSelector: '.trumbowyg-editor',

            /** @string */
            fieldContainerSelector: 'custom-product--option-wrapper-wizard',

            /** @string */
            dateFieldSelector: 'custom-products--date',

            /** @string */
            numberFieldSelector: 'custom-products--numberfield',

            /** @string */
            textAreaSelector: 'custom-products--textarea',

            /** @string */
            textFieldSelector: 'custom-products--textfield',

            /** @string */
            timeFieldSelector: 'custom-products--time',

            /** @string */
            wysiwygFieldSelector: 'custom-products--wysiwyg',

            /** @string */
            triggerDelay: '250'
        },

        /**
         * Initializes the plugin, sets up the necessary elements,
         * checks if all parameters are set and registers the event listeners.
         */
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.$fieldContainer = me.$el.find('.' + me.opts.fieldContainerSelector);

            if (me.requiresEvent()) {
                if (me.class === me.opts.wysiwygFieldSelector) {
                    me.$field = me.$el.find(me.opts.wysiwygSelector);
                }

                if (!me.$field) {
                    me.$field = me.$el.find(me.opts.fieldSelector);
                }

                me._on(me.$field, 'keyup', $.proxy(me.onKeyUp, me));
            }
        },

        /**
         * checks the fieldContainer for a required class
         *
         * @returns {boolean}
         */
        requiresEvent: function() {
            var me = this,
                isWhiteListed = false;

            $.each(me.getClassList(), function(key, value) {
                if (me.$fieldContainer.hasClass(value)) {
                    isWhiteListed = true;
                    me.class = value;
                }
            });

            return isWhiteListed;
        },

        /**
         * creates and returns a array with all fields who could required a change event.
         *
         * @returns {Array}
         */
        getClassList: function() {
            var me = this;

            return [
                me.opts.dateFieldSelector,
                me.opts.numberFieldSelector,
                me.opts.textAreaSelector,
                me.opts.textFieldSelector,
                me.opts.timeFieldSelector,
                me.opts.wysiwygFieldSelector
            ];
        },

        /**
         * the onKeyUp handler creates a delayed function call
         */
        onKeyUp: function() {
            var me = this,
                timeout = me.keyUpTimeout;

            if (timeout) {
                window.clearTimeout(timeout);
            }

            me.keyUpTimeout = window.setTimeout($.proxy(me.triggerChangeEvent, me), me.opts.triggerDelay);
        },

        /**
         * triggers the change event on the element
         */
        triggerChangeEvent: function () {
            this.$el.trigger('change');
        },

        /**
         * Destroy method of the plugin.
         * Removes attached event listener.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me._off(me.$field, 'keyup');

            me._destroy();
        }
    });

    // Plugin starter
    $(function() {
        $('.custom-products--option.is--wizard').swagCustomProductsOptionChange();
    });
})(jQuery, window);
