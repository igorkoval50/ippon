;(function ($, window) {
    'use strict';

    var $html = $('html');

    /**
     * create a new object from $.modal to overwrite some functions
     */
    $.swagModal = $.extend({}, $.modal, {

        /**
         * @overwrite
         *
         * @public
         * @method initModalBox
         */
        initModalBox: function () {
            var me = this;

            me._$body = $('body');
            me._$window = $(window);

            me._$modalBox = $('<div>', {
                'class': 'js--modal'
            });

            me._$header = $('<div>', {
                'class': 'header'
            }).appendTo(me._$modalBox);

            me._$title = $('<div>', {
                'class': 'title'
            }).appendTo(me._$header);

            me._$content.appendTo(me._$modalBox);

            me._$closeButton = $('<div>', {
                'class': 'btn icon--cross is--small btn--grey modal--close'
            }).appendTo(me._$modalBox);

            me._$body.append(me._$modalBox);

            $.publish('plugin/swagCustomProductsModal/onInit', [ me ]);
        },

        /**
         * Disables scrolling on touch enabled devices. We have to disable the default behavior cause
         * the touchmove event will be bubbled up to the html element and would causes the page to scroll.
         *
         * @returns void
         */
        disableScrolling: function () {
            var me = this,
                currentState = window.StateManager.getCurrentState();

            if (currentState !== 'xs' && currentState !== 's') {
                return;
            }

            me._$body.css({
                'position': 'fixed',
                'overflow': 'hidden'
            });
        },

        /**
         * Enables the scrolling of the page on touch enabled devices.
         *
         * @returns void
         */
        enableScrolling: function () {
            var me = this,
                currentState = window.StateManager.getCurrentState();

            if (currentState !== 'xs' && currentState !== 's') {
                return;
            }

            me._$body.css({
                'position': 'inherit',
                'overflow': 'visible'
            });
        },

        /**
         * @overwrite
         *
         * @param options
         * @returns { $.swagModal }
         */
        open: function (options) {
            var me = this,
                $modalBox = me._$modalBox,
                opts;

            me.options = opts = $.extend({}, me.defaults, options);

            if (opts.overlay) {
                $.overlay.open($.extend({}, {
                    closeOnClick: opts.closeOnOverlay,
                    onClose: $.proxy(me.onOverlayClose, me)
                }));
            }

            if (!$modalBox) {
                me.initModalBox();
                me.registerEvents();

                $modalBox = me._$modalBox;
            }

            me._$closeButton.toggle(opts.showCloseButton);

            $modalBox.toggleClass('sizing--auto', opts.sizing === 'auto');
            $modalBox.toggleClass('sizing--fixed', opts.sizing === 'fixed');
            $modalBox.toggleClass('sizing--content', opts.sizing === 'content');
            $modalBox.toggleClass('no--header', opts.title.length === 0);

            $modalBox.addClass(opts.additionalClass);

            if (opts.sizing === 'content') {
                opts.height = 'auto';
            } else {
                $modalBox.css('top', 0);
            }

            me.setTitle(opts.title);
            me.setWidth(opts.width);
            me.setHeight(opts.height);

            // set display to block instead of .show() for browser compatibility
            $modalBox.css('display', 'block');

            me.setTransition({
                opacity: 1
            }, me.options.animationSpeed, 'linear');

            $html.addClass('no--scroll');

            me.disableScrolling();

            $.publish('plugin/swagCustomProductsModal/onOpen', [ me ]);

            return me;
        },

        /**
         * @overwrite
         */
        close: function () {
            var me = this,
                opts = me.options,
                $modalBox = me._$modalBox;

            if (opts.overlay) {
                $.overlay.close();
            }

            $html.removeClass('no--scroll');
            me.enableScrolling();

            $.publish('plugin/swagCustomProductsModal/onClose', [ me ]);

            if ($modalBox !== null) {
                me.setTransition({
                    opacity: 0
                }, opts.animationSpeed, 'linear', function () {
                    $modalBox.removeClass(opts.additionalClass);

                    // set display to none instead of .hide() for browser compatibility
                    $modalBox.css('display', 'none');
                });
            }
        }
    });

    /**
     * The swagCustomProductsWizard plugin
     *
     * This plugin is to create a wizard of all options in a custom product and show
     * it in a modal. Each option step by step.
     */
    $.plugin('swagCustomProductsWizard', {

        /** @object Default plugin configuration */
        defaults: {
            modalWidth: '900px',
            height: 600,
            title: '',

            isHiddenClass: 'is--hidden',
            isActiveClass: 'is--active',

            formCls: '.custom-products--form',

            optionManagerSelector: '*[data-swag-custom-products-option-manager="true"]',
            optionManagerPluginName: 'plugin_swagCustomProductsOptionManager',

            containerClass: '.custom-products--wizard-container',
            optionClass: '.custom-products--option',

            navLeftButtonClass: '.custom-products-navigation--btn-left',
            navRightButtonClass: '.custom-products-navigation--btn-right',
            navSelectClass: '.navigation--select',
            assumeButtonCls: '.take-configuration',

            optionContainerClass: '.wizard-container--options'
        },

        /**
         * Sources for Buttons or SelectBox in the Navigation panel.
         */
        sources: {
            button: 1,
            selectBox: 2
        },

        /**
         * property to store the index of the last selected option
         */
        lastIndex: 0,

        /**
         * Initializes the plugin.
         *
         * @returns void
         */
        init: function () {
            var me = this;

            me.applyDataAttributes();
            me.getExternalPlugins();
            me.createJQueryElements();
            me.subscribeEvents();

            me.$prevButton.prop('disabled', true);
            me.$assumeButton.prop('disabled', true);

            $.swagModal._$content = me.$wizardContent;
        },

        /**
         * the destroy method
         */
        destroy: function () {
            var me = this;

            // rem configure now click
            me.$el.unbind('click');

            // register assume config click
            me.$wizardContent.off('click', me.opts.assumeButtonCls);

            // register navigation events
            me.$wizardContent.off('click', me.opts.navRightButtonClass);
            me.$wizardContent.off('click', me.opts.navLeftButtonClass);
            me.$wizardContent.off('change', me.opts.navSelectClass);

            this._destroy();
        },

        /**
         * Get jQuery plugins from other domElements to interact with it.
         */
        getExternalPlugins: function () {
            var me = this;

            me.optionManagerPlugin = $(me.opts.optionManagerSelector).data(me.opts.optionManagerPluginName);
        },

        /**
         * Selects the necessary DOM elements an get it as jQuery objects.
         */
        createJQueryElements: function () {
            var me = this;

            // get the navigation select
            me.$navSelect = $(me.opts.navSelectClass);
            // get the content
            me.$wizardContent = $(me.opts.containerClass);
            // get the options
            me.$wizardoptions = me.optionManagerPlugin.optionManager.getAllOptions();

            // get the navigation buttons
            me.$nextButton = $(me.opts.navRightButtonClass);
            me.$prevButton = $(me.opts.navLeftButtonClass);

            // get the assume button
            me.$assumeButton = $(me.opts.assumeButtonCls);
        },

        /**
         * Subscribe the necessary events.
         */
        subscribeEvents: function () {
            var me = this;

            // register configure now click
            me.$el.on('click', $.proxy(me.onOpenWizard, me));

            // register assume config click
            me.$wizardContent.on('click', me.opts.assumeButtonCls, $.proxy(me.onAssume, me));

            // register navigation events
            me.$wizardContent.on('click', me.opts.navRightButtonClass, $.proxy(me.onNext, me));
            me.$wizardContent.on('click', me.opts.navLeftButtonClass, $.proxy(me.onPrev, me));
            me.$wizardContent.on('change', me.opts.navSelectClass, $.proxy(me.onChange, me));

            me.$wizardContent.on('change', me.optionManagerPlugin, $.proxy(me.onOptionChange, me));

            // Remove wizard modal box on ajax variant reload if it has been opened before
            $.subscribe('plugin/swAjaxVariant/onBeforeRequestData', function () {
                if ($.swagModal._$modalBox) {
                    $.swagModal._$modalBox.remove();
                    $.swagModal._$modalBox = null;
                }
            });
        },

        /**
         * Set the option manager to the form and close the modal.
         *
         * @param { object } event
         */
        onAssume: function (event) {
            var me = this,
                $customProductsForm = $(me.opts.formCls);

            $customProductsForm.data('plugin_optionManager', me.optionManagerPlugin.optionManager);

            me.optionManagerPlugin.optionManager.onFormChange(event);
            me.optionManagerPlugin.optionManager.triggerRequest(true);

            $.swagModal.close();
        },

        /**
         * Open the modal with the optionContent
         */
        onOpenWizard: function () {
            var me = this;

            $.swagModal.open({ width: me.opts.modalWidth, title: me.opts.title, height: me.opts.height });
            $.swagModal._$content.removeClass(me.opts.isHiddenClass);

            // Call onOptionChange for en / disable initial the button;
            me.onOptionChange();

            // Overwrite the shouldShowOverview because we want to force show the price overview..
            Object.getPrototypeOf(me.optionManagerPlugin.optionManager).shouldShowOverview = function () {
                return true;
            };

            me.optionManagerPlugin.optionManager.triggerRequest();

            $.publish('plugin/swagCustomProductsModal/onOpenWizard', [ me ]);
        },

        /**
         * Option change handler for validate the form...
         * if the form is valid enable the assume Button...
         */
        onOptionChange: function (event) {
            var me = this;

            if (event && $(event.target).closest(me.opts.optionContainerClass).length) {
                me.reloadSurcharges(event);
            }

            if (me.optionManagerPlugin.optionManager.checkValidity(true)) {
                me.toggleNavigationButtons();
                me.$assumeButton.prop('disabled', false);

                $.each(me.optionManagerPlugin.optionManager.getAllOptions(), function () {
                    this.validate();
                });

                $.publish('plugin/swagCustomProductsModal/onOptionChange', [ me ]);

                return;
            }

            $.each(me.optionManagerPlugin.optionManager.getAllOptions(), function () {
                this.validate();
            });

            me.toggleNavigationButtons();
            me.$assumeButton.prop('disabled', true);

            $.publish('plugin/swagCustomProductsModal/onOptionChange', [ me ]);
        },

        toggleNavigationButtons: function () {
            var me = this;

            if (me.$wizardoptions.length <= 1) {
                me.$nextButton.prop('disabled', true);
                me.$prevButton.prop('disabled', true);

                return true;
            }

            return false;
        },

        /**
         * event handler for the previous button.
         */
        onPrev: function () {
            var me = this;

            me.activateNextOption(me.sources.button, me.getPrevIndex(me.lastIndex));

            $.publish('plugin/swagCustomProductsModal/onPrev', [ me ]);
        },

        /**
         * event handler for the next button.
         */
        onNext: function () {
            var me = this;

            me.activateNextOption(me.sources.button, me.getNextIndex(me.lastIndex));

            $.publish('plugin/swagCustomProductsModal/onNext', [ me ]);
        },

        /**
         * Event handler for the navigation selection box.
         */
        onChange: function () {
            var me = this,
                currentValue = me.$navSelect.find(':selected').val() * 1;

            me.activateNextOption(me.sources.selectBox, currentValue);

            $.publish('plugin/swagCustomProductsModal/onChange', [ me ]);
        },

        /**
         * Activates the next options by a boolean that represents an direction.
         * Needs a boolean to find the next index:
         *
         * true is next
         * false is previous
         *
         * @param { int } source
         * @param { int } targetIndex
         */
        activateNextOption: function(source, targetIndex) {
            var me = this;

            me.onOptionChange();

            if (source === me.sources.button) {
                me.$navSelect.val(targetIndex);
            }

            me.enDisAbleButtons(targetIndex);
            me.handleOptionView(me.lastIndex, targetIndex);

            me.lastIndex = targetIndex;

            $.publish('plugin/swagCustomProductsModal/activateNextOption', [me, source, targetIndex, me.$wizardoptions]);
        },

        /**
         * @param { int } currentIndex
         * @param { int } targetIndex
         */
        handleOptionView: function (currentIndex, targetIndex) {
            var me = this;

            me.$wizardoptions[ targetIndex ].$el.addClass(me.opts.isActiveClass);
            me.$wizardoptions[ currentIndex ].$el.addClass(me.opts.isHiddenClass);

            me.$wizardoptions[ targetIndex ].$el.removeClass(me.opts.isHiddenClass);
            me.$wizardoptions[ currentIndex ].$el.removeClass(me.opts.isActiveClass);

            me.$wizardoptions[ currentIndex ].wasSeen = true;

            $.publish('plugin/swagCustomProductsModal/handleOptionView', [ me, currentIndex, targetIndex ]);
        },

        /**
         * @param { int } nextIndex
         */
        enDisAbleButtons: function (nextIndex) {
            var me = this;

            me.$prevButton.prop('disabled', false);
            me.$nextButton.prop('disabled', false);

            if (nextIndex === me.$wizardoptions.length - 1) {
                me.$nextButton.prop('disabled', true);
            }

            if (nextIndex === 0) {
                me.$prevButton.prop('disabled', true);
            }

            $.publish('plugin/swagCustomProductsModal/enDisAbleButtons', [ me ]);
        },

        /**
         * @param { int } index
         * @returns { number }
         */
        getNextIndex: function (index) {
            var me = this;

            if (index === me.$wizardoptions.length - 1) {
                return index;
            }

            return (index + 1);
        },

        /**
         * @param { int } index
         * @returns { number }
         */
        getPrevIndex: function (index) {
            if (index === 0) {
                return index;
            }

            return (index - 1);
        },

        reloadSurcharges: function (event) {
            var me = this;

            me.optionManagerPlugin.optionManager.onFormChange(event);
        }
    });

    $(function () {
        StateManager.addPlugin(
            '.custom-products--open-wizard',
            'swagCustomProductsWizard',
            [ 'm', 'l', 'xl' ]
        );

        StateManager.addPlugin(
            '.custom-products--open-wizard',
            'swagCustomProductsWizard',
            { modalWidth: '100%', height: '100%' },
            [ 'xs', 's' ]
        );
    });
})(jQuery, window);
