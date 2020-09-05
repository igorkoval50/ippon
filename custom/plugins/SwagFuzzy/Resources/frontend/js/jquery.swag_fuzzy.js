;(function ($) {
    'use strict';

    /**
     * This plugin extends the Shopware Live Search Plugin.
     *
     * It highlights the search term in the color of the defined @brand-primary
     */
    $.plugin('swFuzzy', {
        alias: 'swagFuzzy',

        defaults: {
            /**
             * Selector for the suggested names which will be displayed by SwagFuzzy.
             *
             * @type {String}
             */
            fuzzyEntryNameClass: 'fuzzy--entry-name',

            /**
             * Class to highlight the search term in the result items.
             *
             * @type {String}
             */
            ajaxSearchFuzzyHighlightingClass: 'ajax-search--fuzzy-highlighting',

            /**
             * Selector for the default product items which will be displayed in the default behaviour of Shopware.
             *
             * @type {String}
             */
            entryNameSelector: '.entry--name'
        },

        /**
         * Plugin constructor
         */
        init: function () {
            var me = this;

            me.applyDataAttributes();

            /** Register event listener */
            $.subscribe('plugin/swSearch/onSearchResponse', $.proxy(me.onTriggerSearchRequest, me));
        },

        /**
         * Event listener method
         *
         * @param event
         * @param liveSearchPlugin
         * @param searchTerm
         */
        onTriggerSearchRequest: function (event, liveSearchPlugin, searchTerm) {
            var me = this,
                resultNames = me.$el.find(me.opts.entryNameSelector),
                newName;

            /** go trough every search result and highlight the search term */
            resultNames.each(function (index, item) {
                if ($(item).hasClass(me.opts.fuzzyEntryNameClass)) {
                    return true;
                }

                newName = me.highlight($(item).html(), searchTerm);
                $(item).html(newName);
            });
        },

        /**
         * surrounds the search term with a span tag, which colors the search term with @brand-primary
         *
         * @param resultName
         * @param searchTerm
         * @returns newName
         */
        highlight: function (resultName, searchTerm) {
            var me = this,
                regex = new RegExp(searchTerm, 'gi'),
                newName;

            newName = resultName.replace(regex, function (matched) {
                var item = $('<span>', {
                    'class': me.opts.ajaxSearchFuzzyHighlightingClass,
                    'html': matched
                });

                return item[0].outerHTML;
            });

            return newName;
        },

        /** Destroys the plugin */
        destroy: function () {
            $.unsubscribe('plugin/swLiveSearch/onResponseSearchRequest');

            this._destroy();
        }
    });

    $(function () {
        $('.main-search--results').swFuzzy();
    });
})(jQuery);
