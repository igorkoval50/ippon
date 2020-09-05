/*
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

;(function ($, window) {
    'use strict';

    $.plugin('variantInListing', {

        defaults: {

            /**
             * Selector for the product box containing the image container...
             *
             * @property productBoxSelector
             * @type {String}
             */
            productBoxSelector: '.product--box',

            /**
             * Selector for the product image...
             *
             * @property productImageSelector
             * @type {String}
             */
            productImageSelector: '.image--element:first .image--media img',

            /**
             * Selector for the wrapper of variant image container...
             *
             * @property variantWrapperSelector
             * @type {String}
             */
            variantWrapperSelector: '.product--variants--info',

            /**
             * Selector for the variant image item...
             *
             * @property variantImageSelector
             * @type {String}
             */
            variantImageSelector: '.product--variant--imagebox',

            /**
             * Selector for the variant dropdown...
             *
             * @property variantDropdownSelector
             * @type {String}
             */
            variantDropdownSelector: '.product--variant--dropdown',

            /**
             * Selector for the variant item...
             *
             * @property variantImageSelector
             * @type {String}
             */
            variantItemSelector: '.kib-product--variant',
        },

        /**
         * Method for the plugin initialisation.
         * Merges the passed options with the data attribute configurations.
         * Creates and references all needed elements and properties.
         * Calls the registerEvents method afterwards.
         *
         * @public
         * @method init
         */
        init: function () {
            var me = this;

            me.applyDataAttributes();

            /**
             * Reference of the product box container.
             *
             * @private
             * @property _$productBoxContainer
             * @type {jQuery}
             */
            me._$productBoxContainer = me.$el;

            if (!me._$productBoxContainer.length) {
                return;
            }

            me.timeoutTrigger = null;

            me.originalSourceSet = null;

            me.registerEvents();
        },

        /**
         * Registers all needed events of the plugin.
         *
         * @private
         * @method registerEvents
         */
        registerEvents: function () {
            var me = this;

            me.registerProductBoxEvents(me._$productBoxContainer);

            $.subscribe('plugin/swDropdownMenu/onClickMenu', function (event, plugin) {
                if (plugin.$el.hasClass('product--variant--dropdown-trigger')) {
                    $('.product--variant--dropdown-trigger').not(plugin.$el).removeClass(plugin.opts.activeCls);
                }
            });

            StateManager.updatePlugin('.product--variants--info--wrapper[data-kib-variant-slider="true"]', 'swImageSlider');
            StateManager.updatePlugin('.product--variant--dropdown-trigger[data-drop-down-menu="true"]', 'swDropdownMenu');

            $.subscribe('plugin/swEmotionLoader/onLoadEmotionFinished', function (event, plugin) {
                var $productBoxContainer = plugin.$el.find(me.opts.productBoxSelector);

                StateManager.updatePlugin('.product--variants--info--wrapper[data-kib-variant-slider="true"]', 'swImageSlider');
                StateManager.updatePlugin('.product--variant--dropdown-trigger[data-drop-down-menu="true"]', 'swDropdownMenu');
                me.registerProductBoxEvents($productBoxContainer);
            });

            $.subscribe('plugin/swProductSlider/onLoadItemsSuccess', function (event, plugin) {
                var $productBoxContainer = plugin.$el.find(me.opts.productBoxSelector);

                StateManager.updatePlugin('.product--variants--info--wrapper[data-kib-variant-slider="true"]', 'swImageSlider');
                StateManager.updatePlugin('.product--variant--dropdown-trigger[data-drop-down-menu="true"]', 'swDropdownMenu');
                me.registerProductBoxEvents($productBoxContainer);
            });

            $.subscribe('plugin/swInfiniteScrolling/onFetchNewPageFinished', function (event, plugin) {
                var $productBoxContainer = plugin.$el.find(me.opts.productBoxSelector);

                StateManager.updatePlugin('.product--variants--info--wrapper[data-kib-variant-slider="true"]', 'swImageSlider');
                StateManager.updatePlugin('.product--variant--dropdown-trigger[data-drop-down-menu="true"]', 'swDropdownMenu');
                me.registerProductBoxEvents($productBoxContainer);
            });

            $.subscribe('plugin/swInfiniteScrolling/onLoadPreviousFinished', function (event, plugin) {
                var $productBoxContainer = plugin.$el.find(me.opts.productBoxSelector);

                StateManager.updatePlugin('.product--variants--info--wrapper[data-kib-variant-slider="true"]', 'swImageSlider');
                StateManager.updatePlugin('.product--variant--dropdown-trigger[data-drop-down-menu="true"]', 'swDropdownMenu');
                me.registerProductBoxEvents($productBoxContainer);
            });

            $.subscribe('plugin/swListingActions/updateListing', function (event, plugin, html) {
                var $productBoxContainer = plugin.$listing.find(me.opts.productBoxSelector);

                StateManager.updatePlugin('.product--variants--info--wrapper[data-kib-variant-slider="true"]', 'swImageSlider');
                StateManager.updatePlugin('.product--variant--dropdown-trigger[data-drop-down-menu="true"]', 'swDropdownMenu');
                me.registerProductBoxEvents($productBoxContainer);
            });

            //fix for cached objects
            me._on(window, 'beforeunload', function () {
                clearTimeout(me.timeoutTrigger);
            });

            $.publish('plugin/variantInListing/onRegisterEvents', [me]);
        },

        update: function (viewport) {
            var me = this;

            me._$productBoxContainer = me.$el;

            me.registerProductBoxEvents(me._$productBoxContainer);

            $.publish('plugin/variantInListing/onUpdate', [me]);
        },

        registerProductBoxEvents: function ($productBoxContainer) {
            var me = this;

            $.each($productBoxContainer, function (i, el) {
                var $el = $(el),
                    $variantWrapper = $el.find(me.opts.variantWrapperSelector),
                    $variantImageContainer = $variantWrapper.find(me.opts.variantImageSelector),
                    $variantDropdown = $variantWrapper.find(me.opts.variantDropdownSelector);

                if ($variantDropdown.length > 0) {
                    $.each($variantDropdown.children(), function (i, el) {
                        var $el = $(el);

                        me._on($el, 'change', $.proxy(me.onSelectVariant, me, i, $el));
                    });
                }

                if ($variantWrapper.attr('data-cover-delay') > -1) {
                    if ($variantImageContainer.length > 0) {
                        $.each($variantImageContainer, function (i, el) {
                            var $el = $(el);

                            me._on($el, 'mouseenter', $.proxy(me.onMouseEnter, me, i, $el));
                            me._on($el, 'mouseleave', $.proxy(me.onMouseLeave, me, i, $el));
                        });
                    }

                    me._on($el, 'mouseenter', $.proxy(me.onMouseEnterWrapper, me, i, $el));
                    me._on($el, 'mouseleave', $.proxy(me.onMouseLeaveWrapper, me, i, $el));
                }
            });
        },

        onSelectVariant: function (index, $el, event) {
            var me = this;

            window.location.href = $el.val();

            $.publish('plugin/variantInListing/onSelectVariant', [me, $el, event]);
        },


        onMouseEnter: function (index, $el, event) {
            var me = this,
                variantSrcSet = $el.attr('data-listing-cover'),
                $image = $el.parents(me.opts.productBoxSelector).find(me.opts.productImageSelector),
                delay = $el.parents(me.opts.variantWrapperSelector).attr('data-cover-delay');

            window.clearTimeout(me.timeoutTrigger);

            me.timeoutTrigger = window.setTimeout(function () {
                $image.addClass('show--variant');

                if (variantSrcSet != null && variantSrcSet.length > 0) {
                    $image.attr('srcset', variantSrcSet);
                }
            }, delay);

            $.publish('plugin/variantInListing/onMouseEnter', [me, $image, event]);
        },

        onMouseEnterWrapper: function (index, $el, event) {
            var me = this,
                $variantWrapper = $el.find(me.opts.variantWrapperSelector),
                delay = $variantWrapper.attr('data-cover-delay'),
                $image = $el.find(me.opts.productImageSelector);

            if (!$image.hasClass('show--variant') && me.originalSourceSet == null) {
                me.originalSourceSet = $image.attr('srcset');
            }

            if ($el.children().hasClass('variant--slideout') &&
                $variantWrapper.find(me.opts.variantItemSelector).length > 0 &&
                (StateManager.isCurrentState('l') || StateManager.isCurrentState('xl') || StateManager.isCurrentState('xxl'))) {
                window.clearTimeout(me.timeoutTrigger);

                me.timeoutTrigger = window.setTimeout(function () {
                    $variantWrapper.parent().slideDown(100);
                }, delay);
            }

            $.publish('plugin/variantInListing/onMouseWrapperEnter', [me, $variantWrapper, event]);
        },

        onMouseLeave: function (index, $el, event) {
            var me = this,
                $image = $el.parents(me.opts.productBoxSelector).find(me.opts.productImageSelector);

            window.clearTimeout(me.timeoutTrigger);

            if (me.originalSourceSet != null && !($(event.toElement).hasClass(me.opts.variantImageSelector.substring(1)) || $(event.relatedTarget).hasClass(me.opts.variantImageSelector.substring(1)))) {
                $image.removeClass('show--variant');
                $image.attr('srcset', me.originalSourceSet);
            }

            $.publish('plugin/variantInListing/onMouseLeave', [me, $image, event]);
        },

        onMouseLeaveWrapper: function (index, $el, event) {
            var me = this,
                $variantWrapper = $el.find(me.opts.variantWrapperSelector),
                $image = $el.find(me.opts.productImageSelector);

            window.clearTimeout(me.timeoutTrigger);

            if (me.originalSourceSet != null) {
                $image.removeClass('show--variant');
                $image.attr('srcset', me.originalSourceSet);
                me.originalSourceSet = null;
            }

            if ($el.children().hasClass('variant--slideout') &&
                $variantWrapper.find(me.opts.variantItemSelector).length > 0 &&
                (StateManager.isCurrentState('l') || StateManager.isCurrentState('xl') || StateManager.isCurrentState('xxl'))) {
                $variantWrapper.parent().hide();
                $variantWrapper.find('.js--is--dropdown-active').removeClass('js--is--dropdown-active');
            }

            $.publish('plugin/variantInListing/onMouseWrapperLeave', [me, $variantWrapper, event]);
        },

        destroy: function () {
            var me = this;

            me._$productBoxContainer = null;
            me._$variantImageContainer = null;
            me.originalSourceSet = null;
            window.clearTimeout(me.timeoutTrigger);
            me.timeoutTrigger = null;

            me._destroy();
        }
    });

    //fix ghostclick on dropdown
    $.overridePlugin('swDropdownMenu', {
        onClickMenu: function (event) {
            var me = this;

            if (!(event.type === 'click' && 'ontouchstart' in window) || !me.$el.hasClass('product--variant--dropdown-trigger')) {
                me.superclass.onClickMenu.apply(me, arguments);
            }
        },

        onClickBody: function (event) {
            var me = this;

            if (!(event.type === 'click' && 'ontouchstart' in window) || !me.$el.hasClass('product--variant--dropdown-trigger')) {
                me.superclass.onClickBody.apply(me, arguments);
            }
        },
    });

    //fix quickview on slider arrows
    $.overridePlugin('swQuickView', {
        onProductLink: function (event) {
            var me = this;

            if (!$(event.currentTarget).hasClass('arrow')) {
                me.superclass.onProductLink.apply(me, arguments);
            }
        },

        showNext: function () {
            var me = this,
                product = me.products[me.activeProduct],
                index = product.index,
                nextIndex = index + 1;

            if (me.products[nextIndex].$el.hasClass('kib-product--variant-wrapper')) {
                product.$view.setView(me.opts.prevViewState);
                product.$view.prev(me.viewSelector).setView('left');
                product.$view.next(me.viewSelector).setView(me.opts.mainViewState);
                product.$view.next(me.viewSelector).next(me.viewSelector).setView(me.opts.nextViewState);
                me.activeProduct = nextIndex;
                me.showNext();
                return;
            }

            if (!me.products[nextIndex].loaded) {
                me.loadProduct(nextIndex);
            }

            product.$view.setView(me.opts.prevViewState);
            product.$view.prev(me.viewSelector).setView('left');
            product.$view.next(me.viewSelector).setView(me.opts.mainViewState);
            product.$view.next(me.viewSelector).next(me.viewSelector).setView(me.opts.nextViewState);

            me.activeProduct = nextIndex;

            $.publish('plugin/swQuickview/onShowNext', [me]);
        },

        showPrev: function () {
            var me = this,
                product = me.products[me.activeProduct],
                index = product.index,
                prevIndex = index - 1;

            if (me.products[prevIndex].$el.hasClass('kib-product--variant-wrapper')) {
                product.$view.setView(me.opts.nextViewState);
                product.$view.next(me.viewSelector).setView('right');
                product.$view.prev(me.viewSelector).setView(me.opts.mainViewState);
                product.$view.prev(me.viewSelector).prev(me.viewSelector).setView(me.opts.prevViewState);
                me.activeProduct = prevIndex;
                me.showPrev();
                return;
            }

            if (!me.products[prevIndex].loaded) {
                me.loadProduct(prevIndex);
            }

            product.$view.setView(me.opts.nextViewState);
            product.$view.next(me.viewSelector).setView('right');
            product.$view.prev(me.viewSelector).setView(me.opts.mainViewState);
            product.$view.prev(me.viewSelector).prev(me.viewSelector).setView(me.opts.prevViewState);

            me.activeProduct = prevIndex;

            $.publish('plugin/swQuickview/onShowPrev', [me]);
        },
    });

    //fix error on resize
    $.overridePlugin('swImageSlider', {
        updateTransform: function (animate, callback) {
            var me = this;

            if (typeof me._$currentImage[0] !== 'undefined') {
                me.superclass.updateTransform.apply(me, arguments);
            }
        }
    });

    $.subscribe('plugin/swImageSlider/onTrackItems', function (event, plugin) {
            if (plugin.$el.hasClass('product--variants--info--wrapper')) {
                var variantsPerSlide = plugin.$el.children().attr('data-slide-variants');

                if (plugin._itemCount <= variantsPerSlide) {
                    plugin._itemCount = 1;
                } else {
                    plugin._itemCount = plugin._itemCount / variantsPerSlide;
                }
            }
        }
    );

    StateManager.addPlugin(
        '.product--box',
        'variantInListing',
        {},
        ['xs', 's', 'm', 'l', 'xl', 'xxl']
    );

    $.subscribe('plugin/swProductSlider/onLoadItemsSuccess', function (event, plugin) {
        StateManager.updatePlugin('.product--box', 'variantInListing');
    });

    $.subscribe('plugin/swEmotionLoader/onLoadEmotionFinished', function (event, plugin) {
        StateManager.updatePlugin('.product--box', 'variantInListing');
    });
})(jQuery, window);
