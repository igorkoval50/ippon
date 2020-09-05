(function($, window) {

    $.plugin('languageSwitcher', {

        defaults: {

            /**
             * languageSwitcherBtn Selector in TopBar.
             *
             * @type {String}
             */
            languageSwitcherBtn: '.language-switcher--button',

            /**
             * Modal Container Class.
             *
             * @type {String}
             */
            languageSwitcherItemTitleContainerClass: 'language-switcher--title-container block-group',

            /**
             * Logo Selector
             *
             * @type {String}
             */
            languageSwitcherLogoSelector: '.logo--link',

            /**
             * Modal Logo Class.
             *
             * @type {String}
             */
            languageSwitcherLogoClass: 'language-switcher--logo block',

            /**
             * Modal Title Class.
             *
             * @type {String}
             */
            languageSwitcherTitleClass: 'language-switcher--title block',

            /**
             * Modal Text Class.
             *
             * @type {String}
             */
            languageSwitcherTextClass: 'language-switcher--text block',

            /**
             * Modal Text Link Class.
             *
             * @type {String}
             */
            languageSwitcherTextBtnSelector: '.language-switcher--show-all',

            /**
             * Modal Select Element Class.
             *
             * @type {String}
             */
            languageSwitcherSelectClass: 'select-field language-switcher--select block',

            /**
             * Modal Select Name.
             *
             * @type {String}
             */
            languageSwitcherSelectName: 'languageSwitcher',

            /**
             * Modal Select Element Selector.
             *
             * @type {String}
             */
            languageSwitcherSelectSelector: '.language-switcher--select select',

            /**
             * Modal Content Container Element Class.
             *
             * @type {String}
             */
            languageSwitcherContentClass: 'language-switcher--content block-group',

            /**
             * Modal Content Item Element Class.
             *
             * @type {String}
             */
            languageSwitcherItemClass: 'language-switcher--item block',

            /**
             * Modal Content Item Link Element Class.
             *
             * @type {String}
             */
            languageSwitcherItemLinkClass: 'language-switcher-item--link',

            /**
             * Modal Content Item Link Title Class.
             *
             * @type {String}
             */
            languageSwitcherItemTitleClass: 'language-switcher-item--title',

            /**
             * Modal Content Item Media Element Class.
             *
             * @type {String}
             */
            languageSwitcherItemMediaClass: 'language-switcher-item--media',

            /**
             * Shop Language Code.
             *
             * @type {String}
             */
            clientLocale: null,

            /**
             * Class that will be applied to the main plugin element when the menu opens.
             *
             * @type {String}
             */
            activeClass: 'is--shown',

            /**
             * Selector for the trigger element.
             * The trigger is the element that attaches to the click/tap/hover events.
             *
             * @type {String}
             */
            triggerElSelector: '*[data-offcanvas="true"]',

        },

        /**
         * Initializes the plugin
         *
         * @public
         * @method init
         */
        init: function () {
            var me = this,
                opts = me.opts;

            // Config values
            me.shopPath = window.languageSwitcherShopPath || {};
            me.shopLocale = window.languageSwitcherShopLocale.slice(-2);
            me.modalConfigSmallTitle = window.languageSwitcherModalSmallTitle || {};
            me.modalConfigSmallText = window.languageSwitcherModalSmallText || {};
            me.modalConfigTitle = window.languageSwitcherModalTitle || {};
            me.modalConfigContent = window.languageSwitcherModalContent || {};

            me.languageSwitcherBtn = me.$el.find(opts.languageSwitcherBtn);

            /**
             * Element that the events get attached to.
             *
             * @private
             * @property _$triggerEl
             * @type {jQuery}
             */
            me._$triggerEl = $(opts.triggerElSelector);

            /**
             * Modal Content that will be used in the modal box.
             * Will be lazy created only when its needed (on this.$el click).
             *
             * @public
             * @property $template
             * @type {jQuery|null}
             */
            me.$modalContent = null;

            // Register window resize and button events
            me.registerEvents();

        },

        /**
         * Registers the listener for the window resize.
         * Also adds the click/tap listeners for the navigation buttons.
         *
         * @public
         * @method registerEvents
         */
        registerEvents: function () {
            var me = this,
                opts = me.opts;

            $.subscribe(me.getEventName('plugin/languageCountry/getCountryCode'), $.proxy(me.onChangeLanguage, me));
            me._on(me.languageSwitcherBtn, 'click touchstart', $.proxy(me.onClickLanguageSwitcher, me));

            $.publish('plugin/languageSwitcher/onRegisterEvents', [ me ]);
        },

        /**
         * Will be called when Language switcher was clicked
         * to generate and return the modal title
         *
         * @public
         * @method createContent
         *
         */
        createTitle: function (event, modalTitle, modalContent) {
            var me = this,
                opts = me.opts,
                logo,
                title,
                text,
                select,
                option,
                options = '',
                modalTitle;

            if (event.type == 'click') {

                $.each(modalContent, function (index, value) {

                    if (value.title.length) {

                        option = $('<option>', {
                            'value': value.url,
                            'html': value.title
                        });

                        options = options + option.prop('outerHTML');
                    }

                });
            
                logo = $('<div>', {
                    'class': opts.languageSwitcherLogoClass,
                    'html': $(opts.languageSwitcherLogoSelector).html()
                });
    
                title = $('<div>', {
                    'class': opts.languageSwitcherTitleClass,
                    'html': me.modalConfigTitle.title
                });
    
                select = $('<select>', {
                    'name': opts.languageSwitcherSelectName,
                    'html': options
                }).wrap('<div class="' + opts.languageSwitcherSelectClass + '"></div>').parent();
    
                modalTitle = $('<div>', {
                    'class': opts.languageSwitcherItemTitleContainerClass,
                    'html': [
                        logo,
                        title,
                        select
                    ]
                });
                
            } else {

                title = $('<div>', {
                    'class': opts.languageSwitcherTitleClass,
                    'html': me.modalConfigSmallTitle.title
                });

                text = $('<div>', {
                    'class': opts.languageSwitcherTextClass,
                    'html': me.modalConfigSmallText.title
                });

                modalTitle = $('<div>', {
                    'class': opts.languageSwitcherItemTitleContainerClass,
                    'html': [
                        title,
                        text
                    ]
                });
                
            }

            return modalTitle;

            $.publish('plugin/languageSwitcher/generateModalContent', [ me ]);
        },

        /**
         * Will be called when Language switcher was clicked
         * to generate and return the modal content
         *
         * @public
         * @method createContent
         *
         */
        createContent: function (event, modalConfigContent, countryCode) {
            var me = this,
                opts = me.opts,
                title,
                media,
                link,
                item,
                items = '',
                modalContent;

            me.clientLocale = countryCode;

            $.each(modalConfigContent, function(index, value) {

                if (event.type == 'click' && value.title.length ||
                    event.type == 'plugin/languageCountry/getCountryCode' && value.code.indexOf(me.clientLocale) != -1 ||
                    event.type == 'plugin/languageCountry/getCountryCode' && value.code.indexOf(me.shopLocale) != -1) {

                    title = $('<span>', {
                        'class': opts.languageSwitcherItemTitleClass
                    }).text(value.title);

                    media = $('<img>', {
                        'class': opts.languageSwitcherItemMediaClass,
                        'src': value.media
                    });

                    link = $('<a>', {
                        'class': opts.languageSwitcherItemLinkClass,
                        'href': value.url,
                        'html': [
                            media,
                            title
                        ]
                    });

                    item = $('<div>', {
                        'class': opts.languageSwitcherItemClass,
                        'html': [
                            link
                        ]
                    });

                    items = items + item.prop('outerHTML');
                }

            });

            modalContent = $('<div>', {
                'class': opts.languageSwitcherContentClass,
                'html': items
            });

            return modalContent;

            $.publish('plugin/languageSwitcher/generateModalContent', [ me ]);
        },

        /**
         * Called when language switcher button was clicked / touched.
         * Opens the Modal with generated content
         *
         * @public
         * @method onClickLanguageSwitcher
         * @param {jQuery.Event} event
         */
        onClickLanguageSwitcher: function (event) {
            event.preventDefault();

            var me = this,
                opts = me.opts;

            $.overlay.close();

            this.closeMenu();

            me.content = me.createContent(event, me.modalConfigContent);
            me.title = me.createTitle(event, me.modalConfigTitle, me.modalConfigContent);

            $.modal.open(me.content, {
                title: me.title,
                additionalClass: 'language--switcher switcher--big',
                width: 1240,
                sizing: 'content'
            });

            me.languageSwitcher = me.title.find(opts.languageSwitcherSelectSelector);
            me._on(me.languageSwitcher, 'change', $.proxy(me.changeLanguageSwitcherSelect, me, me.languageSwitcher));

            $.publish('plugin/languageSwitcher/onClickLanguageSwitcher', [ me ]);
        },

        /**
         * Called when language switcher detects other language.
         * Opens the Modal with generated content
         *
         * @public
         * @method onChangeLanguage
         * @param {jQuery.Event} event
         */
        onChangeLanguage: function (event, me, countryCode) {
            var me = this,
                opts = me.opts;

            me.clientLocale = countryCode;
            me.localeCheck = StorageManager.getItem('local', 'localeCheck');

            if(me.localeCheck) {
                return;
            }

            if (me.clientLocale != me.shopLocale) {
                $.overlay.close();

                me.content = me.createContent(event, me.modalConfigContent, me.clientLocale);
                me.title = me.createTitle(event, me.modalConfigTitle, me.modalConfigContent);

                $.modal.open(me.content, {
                    title: me.title,
                    additionalClass: 'language--switcher switcher--small',
                    width: 560,
                    sizing: 'content'
                });

                me.languageSwitcherBtn = me.title.find(opts.languageSwitcherTextBtnSelector);
                me._on(me.languageSwitcherBtn, 'click touchstart', $.proxy(me.onClickLanguageSwitcher, me));

                $.publish('plugin/languageSwitcher/onChangeLanguage', [ me ]);
            }

            StorageManager.setItem('local', 'localeCheck', me.clientLocale);

        },


        /**
         * Closes the offcanvas/collapsible cart.
         * If the offcanvas plugin is active on the element, its closeMenu function will also be called.
         *
         * @public
         * @method closeMenu
         */
        closeMenu: function () {
            var plugin;

            // this._isOpened = false;

            if (plugin = this._$triggerEl.data('plugin_swOffcanvasMenu')) {
                plugin.closeMenu();
            } else {
                this.$el.removeClass(this.opts.activeClass);
            }
        },

        /**
         * Called when language switcher select value is changed.
         * Redirects to new site
         *
         * @public
         * @method changeLanguageSwitcherSelect
         * @param {jQuery.Event} event
         */
        changeLanguageSwitcherSelect: function (languageSwitcher, event) {
            window.location = languageSwitcher.val();
        },

        destroy: function () {
            var me = this;
            me._destroy();
        }

    });

    $(function() {
        window.StateManager.addPlugin('body[data-languageSwitcher="true"]','languageSwitcher', ['xs', 's', 'm', 'l', 'xl']);
    });

})(jQuery, window);