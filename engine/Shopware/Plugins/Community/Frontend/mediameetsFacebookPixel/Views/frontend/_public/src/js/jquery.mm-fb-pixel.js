;(function ($, window, document) {
    'use strict';

    /**
     * mediameets Facebook Pixel plugin
     *
     * The plugin handles all Facebook Pixel related initializations, user notifications and tracking.
     *
     * @package     mediameetsFacebookPixel
     * @copyright   2018-2019 media:meets GmbH
     * @author      Marvin Schröder <schroeder@mediameets.de>
     */
    $.plugin('mmFbPixel', {

        /**
         * Default configuration of the plugin
         *
         * @type {Object}
         */
        defaults: {
            /**
             * The plugin privacy mode (optin|optout|active|integrate)
             *
             * @type {String}
             */
            privacyMode: 'integrate',

            /**
             * The Facebook pixel id
             *
             * @type {Null | String}
             */
            facebookPixelID: null,

            /**
             * Additional Facebook pixel ids
             *
             * @type {Null | Array}
             */
            additionalFacebookPixelIDs: null,

            /**
             * The current shopId
             *
             * @type {String}
             */
            shopId: '1',

            /**
             * The advanced matching data
             *
             * @type {Null | Object}
             */
            advancedMatchingData: null,

            /**
             * The autoConfig setting
             *
             * @type {Null | Boolean}
             */
            autoConfig: null,

            /**
             * The url of the plugin data controller
             *
             * @type {Null | String}
             */
            dataController: null,

            /**
             * Events which should be send to Facebook
             *
             * @type {Array}
             */
            events: [],

            /**
             * The configured Shopware cookie_note_mode config value
             *
             * @type {Number}
             */
            swCookieMode: 0,

            /**
             * The configured Shopware show_cookie_note config value
             *
             * @type {Boolean}
             */
            swCookieDisplay: false
        },

        /**
         * Indicator if pixel is loaded
         *
         * @type {Boolean}
         */
        initialized: false,

        /**
         * CSS selectors
         *
         * @type {Object}
         */
        selectors: {
            /**
             * CSS selectors of notification
             *
             * @type {Object}
             */
            notification: {
                /**
                 * CSS selector of the notification itself
                 *
                 * @type {String}
                 */
                base: '#mediameetsFacebookPixel--notification',

                /**
                 * CSS selector for injecting message on close page
                 *
                 * @type {String}
                 */
                closeMessage: '#mediameetsFacebookPixel--close-message',

                /**
                 * CSS selector for setting user choice by link
                 *
                 * @type {String}
                 */
                choiceByLink: '.mediameetsFacebookPixel--choiceByLink',

                /**
                 * CSS selectors of notification buttons
                 *
                 * @type {Object}
                 */
                buttons: {
                    /**
                     * CSS selector of the action button
                     *
                     * @type {String}
                     */
                    action: '.mediameetsFacebookPixel--actionButton',

                    /**
                     * CSS selector of the close button
                     *
                     * @type {String}
                     */
                    close: '.mediameetsFacebookPixel--closeButton'
                }
            }
        },

        /**
         * The default event tracking data
         *
         * @type {Object}
         */
        defaultEventData: {
            language: navigator.language
        },

        /**
         * The standard pixel events given by Facebook
         *
         * @type {Array}
         */
        standardPixelEvents: [
            'PageView',
            'Search',
            'ViewContent',
            'AddToCart',
            'AddToWishlist',
            'InitiateCheckout',
            'AddPaymentInfo',
            'Purchase',
            'Lead',
            'CompleteRegistration',
            'Contact',
            'CustomizeProduct'
        ],

        /**
         * Initializes the plugin and registers event listeners
         *
         * @public
         * @method init
         * @returns {Plugin}
         */
        init: function () {
            var me = this;

            me.localStorage = window.StorageManager.getLocalStorage();

            me.detectUserChoiceByLink();

            if (me.isOptMode() && me.getUserChoice() === null) {
                me.showNotification();
                me.registerEvents();
            }

            if (me.isIntegrationMode()) {
                me.subscribeToConsentManager();
            }

            me.initTracking();
        },

        /**
         * Set user choice by element found on page called via url
         *
         * @public
         * @method detectUserChoiceByLink
         */
        detectUserChoiceByLink: function() {
            var me = this;

            if (!me.isOptMode()) {
                return;
            }

            var $linkChoiceEl = $(me.selectors.notification.choiceByLink + ':first');

            if ($linkChoiceEl.length === 0) {
                return;
            }

            var action = $linkChoiceEl.data('action'),
                mode = me.getMode(),
                choice;

            switch (mode) {
                case 'optin':
                    choice = (action === 'activate') ? 1 : 0;
                    break;
                case 'optout':
                    choice = (action === 'deactivate') ? 1 : 0;
                    break;
            }

            if (action === 'close') {
                var closeMsgEl = $(me.selectors.notification.closeMessage),
                    closeMsgElData = closeMsgEl.data();
                if (typeof closeMsgElData[mode] === 'string') {
                    closeMsgEl.html(closeMsgElData[mode]);
                }
            }

            me.setUserChoice(choice);
        },

        /**
         * Registers events related to notification
         *
         * @public
         * @method registerEvents
         */
        registerEvents: function() {
            var me = this;

            $(me.selectors.notification.buttons.close).on(me.getEventName('click'), $.proxy(me.closeButtonClicked, me));
            $(me.selectors.notification.buttons.action).on(me.getEventName('click'), $.proxy(me.actionButtonClicked, me));
        },

        /**
         * Initializes tracking if enabled:
         * - injects pixel
         * - inits pixel
         * - tracks pageview
         * - registers subscribers
         * - fires stored events
         *
         * @public
         * @method initTracking
         */
        initTracking: function() {
            var me = this;

            if (me.isTrackingEnabled() && me.initialized === false) {
                me.injectPixel();
                me.initPixel();
                me.trackPageview();
                me.registerSubscriber();
                me.fireEvents();
                me.initialized = true;
            }
        },

        /**
         * Reads and returns the user choice from localStorage
         *
         * @public
         * @method getUserChoice
         * @returns {string | null}
         */
        getUserChoice: function() {
            if (!this.isOptMode()) {
                return null;
            }

            return this.localStorage.getItem(this._getStorageKey());
        },

        /**
         * Saves the given user choice in localStorage
         *
         * @public
         * @method setUserChoice
         * @param decision
         */
        setUserChoice: function(decision) {
            if (!this.isOptMode()) {
                return;
            }

            this.localStorage.setItem(this._getStorageKey(), decision);
        },

        /**
         * Returns the current mode
         *
         * @public
         * @method getMode
         * @returns {String | Null}
         */
        getMode: function() {
            return this.opts.privacyMode;
        },

        /**
         *
         */
        subscribeToConsentManager: function() {
            var me = this;

            $.subscribe('plugin/swCookieConsentManager/onBuildCookiePreferences', function () {
                me.initTracking();
            });
        },

        /**
         * Returns true if tracking is enabled
         *
         * @public
         * @method isTrackingEnabled
         * @returns {boolean}
         */
        isTrackingEnabled: function() {
            var me = this,
                opts = me.opts;

            var enabled = false,
                choice = me.getUserChoice(),
                userChoiceIsNullOrZero = (choice === null || parseInt(choice) === 0);

            switch (me.getMode()) {
                case 'optin':
                    enabled = (!userChoiceIsNullOrZero);
                    break;
                case 'optout':
                    enabled = (userChoiceIsNullOrZero);
                    break;
                case 'integrate':
                    if (parseInt(opts.swCookieDisplay) === 0 || parseInt(opts.swCookieMode) === 0) {
                        enabled = true;
                    } else if (parseInt(opts.swCookieMode) === 1 && typeof $.getCookiePreference === 'function') {
                        enabled = $.getCookiePreference('mmFacebookPixel');
                    } else if (parseInt(opts.swCookieMode) !== 0 && document.cookie.indexOf('allowCookie') !== -1) {
                        enabled = true;
                    }
                    break;
                case 'active':
                    enabled = true;
                    break;
            }

            return enabled && opts.facebookPixelID !== null;
        },

        /**
         * Shows the notification
         *
         * @public
         * @method showNotification
         */
        showNotification: function() {
            $(this.selectors.notification.base).hide()
              .removeClass('is--hidden')
              .delay(500)
              .fadeIn();
        },

        /**
         * Hides the notification
         *
         * @public
         * @method hideNotification
         */
        hideNotification: function() {
            $(this.selectors.notification.base).fadeOut();
        },

        /**
         * Sends the given event and (optional) data to Facebook
         *
         * @public
         * @method track
         * @param event
         * @param data
         */
        track: function(event, data) {
            var trackName = ($.inArray(event, this.standardPixelEvents) !== -1) ? 'track' : 'trackCustom',
                eventData = (typeof data === 'undefined') ? null : $.extend({}, this.defaultEventData, data);

            if (!this.isTrackingEnabled() || !window.fbq) {
                return;
            }

            window.fbq(trackName, event, eventData);
        },

        /**
         * Returns true if mode is optin or optout
         *
         * @public
         * @method isOptMode
         * @returns {boolean}
         */
        isOptMode: function() {
            return ['optin', 'optout'].indexOf(this.getMode()) !== -1;
        },

        /**
         * Returns true when mode is integrate
         *
         * @returns {boolean}
         */
        isIntegrationMode: function() {
            return this.getMode() === 'integrate';
        },

        /**
         * Returns the key for localStorage
         *
         * @private
         * @method _getStorageKey
         * @returns {String}
         */
        _getStorageKey: function() {
            return this.getName() + '.' + this.opts.shopId + '.' + this.getMode();
        },

        /**
         * Saves user choice and hides notification
         *
         * @public
         * @method closeButtonClicked
         * @returns {boolean}
         */
        closeButtonClicked: function() {
            this.setUserChoice(0);
            this.hideNotification();

            return false;
        },

        /**
         * Saves user choice and handles action per mode
         * after action button is clicked
         *
         * @public
         * @method actionButtonClicked
         * @returns {boolean}
         */
        actionButtonClicked: function() {
            this.setUserChoice(1);
            this.hideNotification();

            if (this.getMode() === 'optin') {
                this.initTracking();
            }

            return false;
        },

        /**
         * Injects the pixel script from Facebook
         *
         * @public
         * @method injectPixel
         */
        injectPixel: function() {
            if (!this.isTrackingEnabled()) return;

            (function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js'));
        },

        /**
         * Sends the init event to Facebook
         *
         * @public
         * @method initPixel
         */
        initPixel: function() {
            var me = this;

            if (!me.isTrackingEnabled() || !window.fbq) return;

            window.fbq('set', 'autoConfig', (parseInt(me.opts.autoConfig) === 1), me.opts.facebookPixelID);
            window.fbq('init', me.opts.facebookPixelID, me.opts.advancedMatchingData);

            if (me.opts.additionalFacebookPixelIDs !== null) {
                $.each(me.opts.additionalFacebookPixelIDs, function(index, id) {
                    window.fbq('set', 'autoConfig', (parseInt(me.opts.autoConfig) === 1), id);
                    window.fbq('init', id, me.opts.advancedMatchingData);
                });
            }
        },

        /**
         * Sends PageView event to Facebook
         *
         * @public
         * @method trackPageview
         */
        trackPageview: function() {
            this.track('PageView');
        },

        /**
         * Subscribe to Shopware plugin events
         *
         * @public
         * @method registerSubscriber
         */
        registerSubscriber: function() {
            var me = this;

            $.subscribe('plugin/swAjaxVariant/onRequestData.mediameetsFacebookPixel', function (e, plugin) {
                if (!me.isTrackingEnabled() || !window.fbq || me.opts.dataController === null) {
                    return;
                }

                var ordernumber = $.trim(plugin.$el.find(plugin.opts.orderNumberSelector).text());
                if (ordernumber === '') {
                    return;
                }

                var stateObj = plugin._createHistoryStateObject();
                var categoryId = null;

                if (stateObj.params.hasOwnProperty('c')) {
                    categoryId = stateObj.params.c;
                }

                $.get(me.opts.dataController, {
                    ordernumber: ordernumber,
                    selection: decodeURIComponent(stateObj.state.values),
                    categoryId: categoryId,
                    additionalData: ['customization']
                }, function (data) {
                    var productData = $.extend({}, data, {content_type: 'product'});

                    me.track('ViewContent', productData);
                    me.track('ViewProduct', productData);
                    me.track('CustomizeProduct', productData);
                });
            });

            $.subscribe('plugin/swSearch/onSearchRequest.mediameetsFacebookPixel', function (e, plugin, term) {
                if (!me.isTrackingEnabled() || !window.fbq) {
                    return;
                }

                me.track('Search', {
                    search_string: term
                });
            });

            $.subscribe('plugin/swAjaxWishlist/onTriggerRequest.mediameetsFacebookPixel', function(e, plugin, pluginEvent, url) {
                if (!me.isTrackingEnabled() || !window.fbq) {
                    return;
                }

                var urlParts = url.split('/'),
                    orderNumber = urlParts[urlParts.length - 1];

                if (orderNumber === '') {
                    return;
                }

                $.get(me.opts.dataController, {
                    ordernumber: orderNumber
                }, function (data) {
                    me.track('AddToWishlist', $.extend({}, data, {
                        content_type: 'product'
                    }));
                });
            });

            $.subscribe('plugin/swAddArticle/onAddArticle.mediameetsFacebookPixel', function (e, target) {
                if (!me.isTrackingEnabled() || !window.fbq || me.opts.dataController === null) {
                    return;
                }

                var sAddField = target.$el.find('input[name="sAdd"]');
                if (typeof sAddField === 'undefined') {
                    return;
                }

                var orderNumber = sAddField.val();
                if (orderNumber === '' || orderNumber === null) {
                    return;
                }

                var sQuantityField = target.$el.find('input[name="sQuantity"], select[name="sQuantity"]');
                var quantity = (typeof sQuantityField !== 'undefined') ? sQuantityField.first().val() : 1;

                $.get(me.opts.dataController, {
                    ordernumber: orderNumber,
                    quantity: quantity
                }, function (data) {
                    me.track('AddToCart', $.extend({}, data, {
                        content_type: 'product'
                    }));
                });
            });
        },

        /**
         * Fires all queued events
         *
         * @public
         * @method fireEvents
         */
        fireEvents: function() {
            var me = this;

            if (me.opts.events.length === 0) {
                return;
            }

            $.each(me.opts.events, function(index, event) {
                switch (typeof event) {
                    case 'object':
                        $.each(event, function(eventKey, data) {
                            me.track(eventKey, data);
                        });
                        break;
                    case 'string':
                        me.track(event);
                        break;
                }
            });
        },

        /**
         * Destroys the plugin
         *
         * @public
         * @method destroy
         */
        destroy: function () {
            this._destroy();
        }
    });

    window.StateManager.addPlugin('body', 'mmFbPixel', $.extend({}, window.mmFbPixel));
})(jQuery, window, document);
