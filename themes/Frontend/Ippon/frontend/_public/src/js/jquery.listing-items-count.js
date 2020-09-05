;(function ($, window) {
    'use strict';

    $.plugin('listingActionItemsCount', {

        defaults: {
            changeCountBtn: '.count-btn',
            activeCountBntClass: 'is--active',
            listingClass: '.listing',
            loadingOverlaySelector: '.checkout--overlay',
            mobileBoxStorage: 'mobile-box',
            tabletBoxStorage:  'tablet-box',
            tabletLandscapeBoxStorage:  'tabletLandscape-box',
            wrapperMobile: '.mobile-wrapper',
            wrapperTablet: '.tablet-wrapper',
            wrapperTabletLandscape: '.tabletLandscape-wrapper',

            dataMobileDefaultCount: 'data-mobile-count',
            dataTabletDefaultCount: 'data-tablet-count',
            dataTabletLandscapeDefaultCount: 'data-tabletLandscape-count'

        },
        /**
         * Default plugin initialisation function.
         * Sets all needed properties
         * and registers all needed event listeners.
         *
         * @public
         * @method init
         */
        init: function () {
            var me = this;

            me.applyDataAttributes();
            me.$changeCountBtn = $(me.opts.changeCountBtn);
            me.$listingBlock = $(me.opts.listingClass);
            me.dataMobileDefaultCount = me.$listingBlock.attr(me.opts.dataMobileDefaultCount);
            me.dataTabletDefaultCount = me.$listingBlock.attr(me.opts.dataTabletDefaultCount);
            me.dataTabletLandscapeDefaultCount = me.$listingBlock.attr(me.opts.dataTabletLandscapeDefaultCount);
            me.$wrapperMobile = $(me.opts.wrapperMobile);
            me.$wrapperTablet = $(me.opts.wrapperTablet);
            me.$wrapperTabletLandscape = $(me.opts.wrapperTabletLandscape);

            me.storage = StorageManager.getLocalStorage();

            me.defaultBoxSizes();

            me.registerEventListeners();
        },


        defaultBoxSizes: function(device, count) {
            var me = this,
                mobileBoxCount,
                tabletBoxCount,
                tabletLandscapeBoxCount;

            me.applyDataAttributes();

            mobileBoxCount = me.storage.getItem(me.opts.mobileBoxStorage);
            tabletBoxCount = me.storage.getItem(me.opts.tabletBoxStorage);
            tabletLandscapeBoxCount = me.storage.getItem(me.opts.tabletLandscapeBoxStorage);

            // Checker after click
            if (count) {
                if (device == 'mobile') {
                    me.storage.setItem(me.opts.mobileBoxStorage, count);
                    me.$listingBlock.attr('data-mobile-count', count);
                    return;
                }
                else if (device == 'tablet') {
                    me.storage.setItem(me.opts.tabletBoxStorage, count);
                    me.$listingBlock.attr('data-tablet-count', count);
                    return;
                }
                else if (device == 'tabletLandscape') {
                    me.storage.setItem(me.opts.tabletLandscapeBoxStorage, count);
                    me.$listingBlock.attr('data-tabletLandscape-count', count);
                    return;
                }

            }
            // Mobile start or default checker
            if (mobileBoxCount) {
                me.$listingBlock.attr('data-mobile-count', mobileBoxCount);
                if (mobileBoxCount == "1") {
                    me.$wrapperMobile.children('.count-btn[data-box-count="1"]').addClass(me.opts.activeCountBntClass)
                }
                if (mobileBoxCount == "2") {
                    me.$wrapperMobile.children('.count-btn[data-box-count="2"]').addClass(me.opts.activeCountBntClass)
                }
            }
            else {
                me.storage.setItem(me.opts.mobileBoxStorage, me.dataMobileDefaultCount);
                me.$listingBlock.attr('data-mobile-count', me.dataMobileDefaultCount);
                me.$wrapperMobile.children('.count-btn[data-box-count="2"]').addClass(me.opts.activeCountBntClass);
            }
            // Tablet start or default checker
            if (tabletBoxCount) {
                me.$listingBlock.attr('data-tablet-count', tabletBoxCount);
                if (tabletBoxCount == "2") {
                    me.$wrapperTablet.children('.count-btn[data-box-count="2"]').addClass(me.opts.activeCountBntClass)
                }
                if (tabletBoxCount == "3") {
                    me.$wrapperTablet.children('.count-btn[data-box-count="3"]').addClass(me.opts.activeCountBntClass)
                }
            }
            else {
                me.storage.setItem(me.opts.tabletBoxStorage, me.dataTabletDefaultCount);
                me.$listingBlock.attr('data-tablet-count', me.dataTabletDefaultCount);
                me.$wrapperTablet.children('.count-btn[data-box-count="3"]').addClass(me.opts.activeCountBntClass);
            }
            // TabletLandscape start or default checker
            if (tabletLandscapeBoxCount) {
                me.$listingBlock.attr('data-tabletLandscape-count', tabletLandscapeBoxCount);
                if (tabletLandscapeBoxCount == "3") {
                    me.$wrapperTabletLandscape.children('.count-btn[data-box-count="3"]').addClass(me.opts.activeCountBntClass)
                }
                if (tabletLandscapeBoxCount == "4") {
                    me.$wrapperTabletLandscape.children('.count-btn[data-box-count="4"]').addClass(me.opts.activeCountBntClass)
                }

            }
            else {
                me.storage.setItem(me.opts.tabletLandscapeBoxStorage, me.dataTabletLandscapeDefaultCount);
                me.$listingBlock.attr('data-tabletLandscape-count', me.dataTabletLandscapeDefaultCount);
                me.$wrapperTabletLandscape.children('.count-btn[data-box-count="3"]').addClass(me.opts.activeCountBntClass);
            }

        },

        registerEventListeners: function () {
            var me = this;
            me.$changeCountBtn.each(function (i, el) {
                me._on(el, 'click touchstart', $.proxy(me.changeListingCount, me));
            });
        },

        changeListingCount: function (event) {
            var me = this,
                boxCount;

            boxCount = $(event.target).attr('data-box-count');

            $(event.target).parent().find(me.$changeCountBtn).removeClass(me.opts.activeCountBntClass);
            $(event.target).addClass(me.opts.activeCountBntClass);


            if (window.innerWidth <= 767) {
                me.defaultBoxSizes('mobile', boxCount);
            }
            else if (window.innerWidth <= 1023) {
                me.defaultBoxSizes('tablet' ,boxCount);
            }
            else {
                me.defaultBoxSizes('tabletLandscape', boxCount);
            }
        }
    });

    window.StateManager.addPlugin('.action--count-btn', 'listingActionItemsCount');

})(jQuery, window);
