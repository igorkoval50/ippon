;(function($, window) {
    'use strict';

    $.plugin('swLiveShoppingListing', {

        /** Your default options */
        defaults: {
            liveShoppingProductBoxSelector: '*[data-live-shopping-listing-product="true"]',

            liveShoppingListingUpdateUrl: '',

            validTo: null,

            currencyFormat: null,
        },

        /**
         * Initializes the plugin
         */
        init: function () {
            var me = this;

            me.liveShoppingProductBoxes = [];
            me.addEvent = true;

            me.applyDataAttributes();
            me.loadProductBoxes();
            me.registerEvents();
            me.updateLiveShoppingData();
        },

        loadProductBoxes: function() {
            var me = this,
                boxes = $(me.opts.liveShoppingProductBoxSelector);

            me.updateBoxList(boxes);
        },

        registerEvents: function() {
            var me = this;

            $.subscribe(me.getEventName('plugin/swInfiniteScrolling/onLoadPreviousFinished'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swInfiniteScrolling/onFetchNewPageFinished'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swListingActions/onGetFilterResultFinished'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swLastSeenProducts/onCreateProductList'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swProductSlider/onLoadItemsSuccess'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swEmotionLoader/onInitEmotion'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swAjaxVariant/onRequestData'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swQuickview/onProductLoaded'), $.proxy(me.loadProductBoxes, me));
            $.subscribe(me.getEventName('plugin/swProductSlider/onInitSlider'), $.proxy(me.loadProductBoxes, me));

            $.subscribe('plugin/swagLiveShopping/triggerUpdatePrice', $.proxy(me.updateLiveShoppingData, me));
        },

        /**
         * @param boxes {Array}
         * @return {number}
         */
        updateBoxList: function(boxes) {
            var me = this;

            $.each(boxes, function(index, box) {
                var $box = new SwagLiveShoppingProduct($(box), me.getCurrencyFormat(), me.addEvent);

                if ($box.isRegistered()) {
                    return;
                }

                $box.register();
                me.liveShoppingProductBoxes.push($box);
                me.addEvent = false;
            });

            me.liveShoppingIds = me.liveShoppingProductBoxes.map(function(x) {
                return x.getLiveShoppingId();
            });
        },

        updateLiveShoppingData: function() {
            var me = this,
                tempLiveShoppingData = {};

            if (!me.liveShoppingIds.length) {
                return;
            }

            if (me.ajax) {
                me.ajax.abort();
            }

            me.ajax = $.ajax({
                method: "POST",
                url: me.opts.liveShoppingListingUpdateUrl,
                data: {
                    liveShoppingIds: me.liveShoppingIds
                }
            }).done(function(response) {
                if (!response.success) {
                    return;
                }

                $.each(response.data, function(liveShoppingId, liveShopping) {
                    tempLiveShoppingData[liveShoppingId] = liveShopping;
                });

                $.each(me.liveShoppingProductBoxes, function(index, $box) {
                    $box.updatePrice(tempLiveShoppingData[$box.getLiveShoppingId()].currentPrice);
                });

                me.ajax = null;
            });
        },

        /**
         * @return {string}
         */
        getCurrencyFormat: function() {
            return this.opts.currencyFormat;
        },
    });

    /**
     * @param $box {Object}
     * @param currencyFormat {string}
     * @param fireEvent {boolean}
     *
     * @constructor
     */
    function SwagLiveShoppingProduct($box, currencyFormat, fireEvent) {
        var me = this;

        me.$el = $box;
        me.currencyFormat = currencyFormat;
        me.fireEvent = fireEvent;

        me.init();
    }

    SwagLiveShoppingProduct.prototype = {
        $el: null,
        currencyFormat: null,
        fireEvent: false,

        init: function() {
            var me = this,
                second = 1000,
                date = new Date(),
                newDate = (date.getTime() / second) - 1;

            me.date = date;

            me.initDetail();
            me.initElements();

            me.timeRunner = new window.TimeRunner(newDate, second, me.updateTime, me);

            me.liveShoppingCounterContainer.show();
            me.liveShoppingPriceContainer.show();
        },

        initDetail: function() {
            var me = this,
                defaultPriceSelector = '.price--default',
                defaultUnitSelector = '.price--unit';

            me.isDetail = me.$el.hasClass('liveshopping--details');

            if (!me.isDetail) {
                return
            }

            $(defaultPriceSelector).hide();
            $(defaultUnitSelector).hide();

            me.$el.show();
        },

        initElements: function() {
            var me = this;

            me.liveShoppingId = me.$el.attr('data-liveshoppingid');
            me.validTo = me.$el.attr('data-validTo');

            me.liveShoppingPriceContainer = me.$el.find('.liveshopping--container');
            me.liveShoppingCounterContainer = me.$el.find('.badge--liveshopping');

            me.dayElement = me.$el.find('.liveshopping--days');
            me.hourElement = me.$el.find('.liveshopping--hours');
            me.minuteElement = me.$el.find('.liveshopping--minutes');
            me.secondElement = me.$el.find('.liveshopping--seconds');
            me.elapseElement = $('.elapse--inner');

            me.priceElement = me.$el.find('.liveshopping--price');

            if (!me.isDetail) {
                return
            }

            me.bonusPointElement = $('.bonussystem--info');
        },

        /**
         * @return {number}
         */
        getLiveShoppingId: function() {
            return this.liveShoppingId;
        },

        updateTime: function() {
            var me = this,
                timeNow = new Date(),
                validTo = new Date(me.validTo * 1000),
                diff = me.getTimestampDiff(validTo.getTime(), timeNow.getTime());

            if (me.fireEvent && (diff.s + 1) % 20 === 0) {
                $.publish('plugin/swagLiveShopping/triggerUpdatePrice', [
                    me
                ]);
            }

            me.refreshDates(diff);
        },

        /**
         * @param diff {Object}
         */
        refreshDates: function(diff) {
            var me = this,
                percentage = diff.s / 60 * 100;

            me.dayElement.html(diff.d);
            me.hourElement.html(diff.h);
            me.minuteElement.html(diff.m);
            me.secondElement.html(diff.s);

            if (!me.elapseElement) {
                return
            }

            me.elapseElement.css('width', percentage + '%');
        },

        /**
         * @param price {number}
         */
        updatePrice: function(price) {
            var me = this;

            me.priceElement.fadeOut('fast');
            me.priceElement.html(me.formatCurrency(price));
            me.priceElement.fadeIn('slow');

            if (me.isDetail) {
                me.updateBonusPoints(price);
            }
        },

        /**
         * @param value {number}
         *
         * @return {string}
         */
        formatCurrency: function(value) {
            var me = this,
                currencyFormat = me.currencyFormat;

            value = Math.round(value * 100) / 100;
            value = value.toFixed(2);
            if (currencyFormat.indexOf('0.00') > -1) {
                value = currencyFormat.replace('0.00', value);
            } else {
                value = value.replace('.', ',');
                value = currencyFormat.replace('0,00', value);
            }

            return value;
        },

        /**
         * @param d1 {number}
         * @param d2 {number}
         *
         * @return {Object|boolean}
         */
        getTimestampDiff: function(d1, d2) {
            var me = this;

            if (d1 < d2) {
                return false;
            }

            var d = Math.floor((d1 - d2) / (24 * 60 * 60 * 1000));
            var h = Math.floor(((d1 - d2) - (d * 24 * 60 * 60 * 1000)) / (60 * 60 * 1000));
            var m = Math.floor(((d1 - d2) - (d * 24 * 60 * 60 * 1000) - (h * 60 * 60 * 1000)) / (60 * 1000));
            var s = Math.floor(((d1 - d2) - (d * 24 * 60 * 60 * 1000) - (h * 60 * 60 * 1000) - (m * 60 * 1000)) / 1000);

            return {
                'd': me.formatNumber(d),
                'h': me.formatNumber(h),
                'm': me.formatNumber(m),
                's': me.formatNumber(s)
            };
        },

        /**
         * @param number {number}
         *
         * @return {string}
         */
        formatNumber: function(number) {
            var tmp = number + '';
            if (tmp.length === 1) {
                return '0' + number;
            } else {
                return number;
            }
        },

        /**
         * Helper function to set the bonus points depending on the current live shopping price
         *
         * @param currentPrice
         */
        updateBonusPoints: function(currentPrice) {
            var me = this,
                bonusPlugin;

            if (!me.bonusPointElement.length) {
                return;
            }

            bonusPlugin = $('.content.product--details').data('plugin_swBonusSystemDetail');
            bonusPlugin.productPrice = currentPrice;
            bonusPlugin.isLiveShopping = true;
            bonusPlugin.getDetailPoints();
        },

        register: function() {
            this.$el.attr('swagLiveShoppingListingBox', 1);
        },

        /**
         * @return {string}
         */
        isRegistered: function() {
            return this.$el.attr('swagLiveShoppingListingBox');
        },
    };

    /**
     * @param timeNow {number}
     * @param timeout {number}
     * @param timerCallback {number}
     * @param scope {object}
     *
     * @constructor
     */
    function Server(timeNow, timeout, timerCallback, scope) {
        this.timeNow = timeNow * 1000;
        this.timeOut = timeout;
        this.timerCallback = timerCallback;
        this.callbackScope = scope;
        this.init();
    }

    Server.prototype = {
        timeNow: undefined,
        timeOut: undefined,
        interval: undefined,
        timerCallback: undefined,

        init: function() {
            var me = this;

            me.interval = window.setInterval(function() {
                me.setTime();
            }, me.timeOut);
        },

        setTime: function() {
            this.timeNow += this.timeOut;
            if ($.isFunction(this.timerCallback)) {
                this.timerCallback.apply(this.callbackScope, [this.timeNow]);
            }
        },

        shutdown: function() {
            window.clearInterval(this.interval);
            this.interval = undefined;
        }
    };

    window.TimeRunner = Server;

    $('*[data-live-shopping-listing="true"]').swLiveShoppingListing();

})(jQuery, window);
