;(function($) {
    'use strict';

    $.plugin('swBundleOffcanvasDescription', {

        defaults: {
            /**
             * @type String
             */
            contentLongSelector: '.teaser--text-long',

            /**
             * @type String
             */
            contentShortSelector: '.teaser--text-short',

            /**
             * @type String
             */
            offcanvasOpenTriggerSelector: '.text--offcanvas-link',

            /**
             * @type String
             */
            offcanvasContentSelector: '.teaser--text-offcanvas',

            /**
             * @type String
             */
            offCanvasCloseSelector: '.close--off-canvas',

            /**
             * @type String
             */
            offCanvasDirection: 'fromRight',

            /**
             * @type String
             */
            hiddenClass: 'is--hidden'
        },

        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.findElements();

            me.initializeViewStatus();

            me.registerEvents();
        },

        findElements: function() {
            var me = this;

            me.contentLongElement = me.$el.find(me.opts.contentLongSelector);
            me.contentShortElement = me.$el.find(me.opts.contentShortSelector);
            me.offcanvasContentElement = me.$el.find(me.opts.offcanvasContentSelector);
            me.offcanvasOpenTriggerElement = me.$el.find(me.opts.offcanvasOpenTriggerSelector);

            me.offcanvasOpenTriggerElement.swOffcanvasMenu({
                'offCanvasSelector': me.opts.offcanvasContentSelector,
                'closeButtonSelector': me.opts.offCanvasCloseSelector,
                'direction': me.opts.offCanvasDirection
            });

            me.plugin = me.offcanvasOpenTriggerElement.data('plugin_swOffcanvasMenu');
        },

        initializeViewStatus: function() {
            var me = this;

            me.contentLongElement.addClass(me.opts.hiddenClass);
            me.contentShortElement.removeClass(me.opts.hiddenClass);
            me.offcanvasContentElement.addClass(me.opts.hiddenClass);
        },

        registerEvents: function() {
            var me = this;

            me.offcanvasOpenTriggerElement.click($.proxy(me.onOpen, me));

            $.subscribe(me.getEventName('plugin/swOffcanvasMenu/onCloseMenu'), $.proxy(me.onClose, me));
        },

        onOpen: function() {
            var me = this,
                contentElements = $(me.opts.offcanvasContentSelector);

            $.each(contentElements, function(index, contentElement) {
                $(contentElement).addClass(me.opts.hiddenClass);
            });

            me.offcanvasContentElement.removeClass(me.opts.hiddenClass);
        },

        destroy: function() {
            var me = this;

            me.contentLongElement.removeClass(me.opts.hiddenClass);
            me.contentShortElement.addClass(me.opts.hiddenClass);
            me.offcanvasContentElement.addClass(me.opts.hiddenClass);

            me.plugin.destroy();
        },

        onClose: function() {
            this.initializeViewStatus();
        }
    });
})(jQuery);
