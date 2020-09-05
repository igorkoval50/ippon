(function($, window) {

    $.plugin('themescripts', {

        defaults: {

            affixOffset: 300,
            scrollToTopOffset: 200,
            inquirySelector: 'input[name="inquiry"]'

        },

        /**
         * Initializes the plugin
         *
         * @public
         * @method init
         */
        init: function () {
            var me = this,
                $el = me.$el,
                opts = me.opts;

            me.affix();
            me.scrollToTop();
            me.inquiryReadonly();

            $.subscribe('plugin/swEmotionLoader/onLoadEmotionFinished', function() {
                //wenn die einkaufswelt komplett geladen ist
                //hier den slider initialisieren
            });

        },

        /*
        *
        * Init Affix for header--navigation
        *
        */
        affix: function() {
            var me = this;

            $('.header-wrap').affix({
              offset: {
                top: me.opts.affixOffset
              }
            });
        },

        /*
        *
        * Scroll To Top
        *
        */
        scrollToTop: function() {
            var me = this;

            $('#scroll-to-top').click(function () {
                $('html, body').animate({ scrollTop: 0 }, me.opts.scrollToTopOffset);
                return false;
            });
        },

        inquiryReadonly: function() {
            var me = this,
                opts = me.opts,
                $el = me.$el;

            me.$inquiry = $el.find(opts.inquirySelector);
            me.$inquiry.prop( "readonly", true );
        },

        destroy: function () {
            var me = this;
            me._destroy();
        }
    
    });

    $(function() {
        window.StateManager.addPlugin('.page-wrap','themescripts', ['m', 'l', 'xl']);
    });


    window.StateManager
        .addPlugin('.collapse--link-category', 'swCollapsePanel', {
            contentSiblingSelector: '.collapse--categories-navigation'
        })
        .addPlugin('.category--teaser .hero--text', 'swOffcanvasHtmlPanel', ['xs', 's', 'm', 'l'])
        .addPlugin('*[data-offcanvasmobile="true"]', 'swOffcanvasMenu', ['xs', 's', 'm', 'l'])
        .addPlugin('*[data-offcanvas="true"]', 'swOffcanvasMenu', ['xs', 's', 'm', 'l'])
        .addPlugin('*[data-offcanvasFilter="true"]', 'swOffcanvasMenu', ['xs', 's'])
        .addPlugin('*[data-subcategory-nav="true"]', 'swSubCategoryNav', ['xs', 's', 'm', 'l'])

        .removePlugin('.navigation--entry.entry--account.with-slt', 'swDropdownMenu', ['m', 'l', 'xl'])
        .addPlugin('.navigation--entry.entry--account.with-slt', 'swDropdownMenu', ['xl'])

        .addPlugin('.custom-collapse .custom-collapse-headline', 'swCollapsePanel', {
            contentSiblingSelector: '.custom-collapse-container'
        })
        .addPlugin('.custom-collapse .custom-collapse-quick-order-headline', 'swCollapsePanel', {
            contentSiblingSelector: '.custom-collapse-quick-order-container'
        })

})(jQuery, window);
