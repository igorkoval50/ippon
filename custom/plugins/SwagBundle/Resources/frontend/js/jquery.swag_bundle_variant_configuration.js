;(function ($) {
    'use strict';

    $.plugin('swagBundleVariantConfigurationSave', {

        /** The default options */
        defaults: {

            /**
             * @type Number
             */
            bundleId: 0,

            /**
             * @type String
             */
            configurationRowClass: '.bundle--product-configuration',

            /**
             * @type String
             */
            selectFieldSelector: ' .configuration-selector select',

            /**
             * @type String
             */
            configuratorFormSelectSelector: 'form.configurator--form select',

            /**
             * @type String
             */
            formSelector: 'form[class="bundle--form"]',

            /**
             * @type Number
             */
            productDetailId: 0,

            /**
             * @type String
             */
            updatePriceUrl: '',

            /**
             * @type String
             */
            priceLabelSelector: '.price--value-bundle-price',

            /**
             * @type String
             */
            regularPriceLabelSelector: '.regular-price-total',

            /**
             * @type String
             */
            bundleAddToCartButtonSelector: '.bundle--add-to-cart-button',

            /**
             * @type String
             */
            bundleIsUnavailable: '',

            /**
             * @type String
             */
            bundleIsOutOfStock: '',

            /**
             * @type string
             */
            bundleDeliverySelectorPrefix: '.bundle-delivery-selector-',

            /**
             * @type string
             */
            bundlePurchaseUnitSelectorPrefix: '.bundle--purchaseUnit-',

            /**
             * @type string
             */
            bundleReferencePriceSelectorPrefix: '.bundle--reference-price-',

            /**
             * @type string
             */
            bundlePriceSelectorTemplate: 'span[data-bundleproductid="%s"]',

            /**
             * @type string
             */
            bundleReferencePriceDescriptionSelectorTemplate: 'span[class="bundle--purchaseDescription-%s"]'
        },


        /**
         * Initializes the plugin
         */
        init: function () {
            var me = this;

            // Applies HTML data attributes to the default options
            me.applyDataAttributes();

            me.findElements();

            me.registerEventListener();

            me.onBaseProductChangeVariant();
        },

        /**
         * Collects and sets properties which are jQuery elements
         */
        findElements: function () {
            var me = this;

            me.$bundleSelects = me.$el.find(me.opts.configurationRowClass + me.opts.selectFieldSelector);
            me.$bundleSelects.each(function (index, element) {
                me[element.name] = $(element);
            });

            me.bundleAddToCartButton = me.$el.find(me.opts.bundleAddToCartButtonSelector);
        },

        /**
         * Registers all event listeners
         */
        registerEventListener: function () {
            var me = this;

            $.subscribe('swagBundle/updateBundleContainer', $.proxy(me.onBaseProductChangeVariant, me));
            $.subscribe('swagBundle/productSelection/change', $.proxy(me.onBundleProductVariantChange, me));
            $.subscribe('plugin/swCsrfProtection/init', $.proxy(me.registerEventListener, me));

            me.$bundleSelects.each(function (index, element) {
                me[element.name].on('change', $.proxy(me.onBundleProductVariantChange, me));
            });
        },

        /**
         * Event handler to get the price for the current bundle configuration.
         */
        onBundleProductVariantChange: function () {
            var me = this,
                formData = me.$el.closest(me.opts.formSelector).serializeArray().reduce(
                    function (obj, item) {
                        obj[item.name] = item.value;
                        return obj;
                    },
                    {}
                );

            me.resetInactiveVariant();
            formData.bundleId = me.opts.bundleId;

            if (me.timeOut) {
                clearTimeout(me.timeOut);
            }

            me.timeOut = setTimeout(function () {
                me.callNewPrice(formData);
                me.timeOut = null;
            }, 300);
        },

        /**
         * @param {object} formData
         */
        callNewPrice: function (formData) {
            var me = this;

            if (me.currentRequest) {
                me.currentRequest.abort();
            }

            me.currentRequest = $.ajax({
                url: me.opts.updatePriceUrl,
                data: formData,
                method: 'POST'
            }).done(function (response) {
                me.currentRequest = null;

                if (response.success === false) {

                    if (response.notActive) {
                        me.handleInactiveVariant(response.id, me.opts.bundleIsUnavailable);
                        return;
                    }

                    me.handleInactiveVariant(response.id, me.opts.bundleIsOutOfStock);
                    return;
                }

                var $priceSpan = me.$el.find("*" + me.opts.priceLabelSelector),
                    $regularPriceSpan = me.$el.find(me.opts.regularPriceLabelSelector),
                    bundleProductId, prices;

                $.each(Object.keys(response.prices.productPrices), function (index, priceKey) {
                    bundleProductId = priceKey.replace(/'/g, '');
                    prices = response.prices.productPrices[priceKey];

                    if (prices.referencePrice.unit !== undefined) {
                        me.$el.find(
                            me.opts.bundleReferencePriceDescriptionSelectorTemplate.replace('%s', bundleProductId)
                        ).html(prices.referencePrice.unit.description);
                    }

                    me.$el.find(me.opts.bundlePriceSelectorTemplate.replace('%s', bundleProductId)).html(prices.price);
                    me.$el.find(me.opts.bundlePurchaseUnitSelectorPrefix + bundleProductId).html(prices.referencePrice.purchaseUnit);
                    me.$el.find(me.opts.bundleReferencePriceSelectorPrefix + bundleProductId).html(prices.referencePrice.referencePrice.display);
                });

                $priceSpan.html(response.prices.price);
                $regularPriceSpan.html(response.prices.regularPrice);
            });
        },

        handleInactiveVariant: function (id, reasonText) {
            var me = this,
                reasonTextTemplate = '<span class="bundle--product-not-available">%s</span>';

            me.deliveryTextContainer = me.$el.find(me.opts.bundleDeliverySelectorPrefix + id);
            me.deliveryTextContainerDefaultText = me.deliveryTextContainer.html();
            me.deliveryTextContainer.html(reasonTextTemplate.replace('%s', reasonText));
            me.bundleAddToCartButton.attr('disabled', 'disabled');
        },

        resetInactiveVariant: function () {
            var me = this;

            if (!me.deliveryTextContainer) {
                return;
            }

            me.deliveryTextContainer.html(me.deliveryTextContainerDefaultText);
            me.bundleAddToCartButton.removeAttr('disabled');
        },

        /**
         * Synchronizes the variant of the main product with the main product of the bundle
         */
        onBaseProductChangeVariant: function () {
            var me = this,
                bundleSelectId,
                $bundleSelect;

            $(me.opts.configuratorFormSelectSelector).each(function (index, element) {
                bundleSelectId = me.transformCoreSelectName(element.name);
                $bundleSelect = $(bundleSelectId);
                $bundleSelect.val(element.value);
                $bundleSelect.trigger('change');
            });
        },

        /**
         * @param {string} coreSelectName
         * @return {string}
         */
        transformCoreSelectName: function (coreSelectName) {
            var me = this;

            return [
                'select[name="group-0::',
                me.opts.productDetailId,
                '::',
                coreSelectName.replace('group[', '').replace(']', ''),
                '"]'
            ].join('');
        },

        /**
         * Destroys the plugin and removes event listener
         */
        destroy: function () {
            var me = this;

            $.unsubscribe('swagBundle/updateBundleContainer');
            $.unsubscribe('swagBundle/productSelection/change');
            $.unsubscribe('plugin/swCsrfProtection/init');

            me.$bundleSelects.each(function (index, element) {
                me[element.name].off('change');
            });
        }
    });

    /** Plugin starter */
    $(function () {
        StateManager.addPlugin('*[data-swagBundleVariantConfiguration="true"]', 'swagBundleVariantConfigurationSave');
    });
}(jQuery, window, location));
