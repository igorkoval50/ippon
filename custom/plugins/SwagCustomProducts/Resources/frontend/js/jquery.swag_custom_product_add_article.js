;(function($, window) {
    'use strict';

    $.plugin('swagCustomProductsSwAddArticle', {

        /** @object Default plugin configuration */
        defaults: {

            /** @string selector for the node on which the plugin is registered */
            swAddArticlePluginObjectSelector: '*[data-add-article="true"]',

            /** @string Error overview selector */
            errorOverviewSelector: '.custom-products--global-error-overview',

            /** @string buyFormSelector */
            buyFormSelector: '.buybox--form',

            /** @string hashInputSelector */
            hashInputSelector: 'input[name=customProductsHash]',

            /** @string customProductFormSelector */
            customProductFormSelector: '.custom-products--form',

            /** @string templateIdAttr */
            templateIdAttr: 'data-templateId',

            /** @string overviewNumberAttr */
            overviewNumberAttr: 'data-overview-number',

            /** @string customUrlAttr */
            customUrlAttr: 'data-custom-url',

            /** @string hashInputName */
            hashInputName: 'customProductsHash',

            /** @string optionManagerPluginName */
            optionManagerPluginName: 'plugin_optionManager',

            /** @string swAddArticlePluginName */
            swAddArticlePluginName: 'plugin_swAddArticle',

            /** @string buyButtonSelector */
            buyButtonSelector: '.buybox--button'
        },

        /**
         * Initializes the plugin, sets up the necessary elements,
         * registers the event listener.
         */
        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.swAddArticlePlugin = $(me.opts.swAddArticlePluginObjectSelector).data(me.opts.swAddArticlePluginName);
            me.$buyBoxButton = me.swAddArticlePlugin.$el;
            me.originalFunction = $.proxy(me.swAddArticlePlugin.sendSerializedForm, me.swAddArticlePlugin);

            // unbind click event from the addArticle plugin
            me.swAddArticlePlugin._off(me.$buyBoxButton, me.swAddArticlePlugin.opts.eventName);

            // register own click event
            me._on(
                me.$buyBoxButton,
                me.swAddArticlePlugin.opts.eventName,
                $.proxy(me.sendSerializedForm, me)
            );
        },

        /**
         * Removes the registered eventListeners
         *
         * @return void
         */
        destroy: function() {
            var me = this;

            // ReAdd the original event listener to the addArticle plugin
            me.swAddArticlePlugin._on(
                me.$buyBoxButton,
                me.swAddArticlePlugin.opts.eventName,
                $.proxy(me.swAddArticlePlugin.sendSerializedForm, me.swAddArticlePlugin)
            );

            me._off(
                me.$buyBoxButton,
                me.swAddArticlePlugin.opts.eventName
            );

            me._destroy();
        },

        /**
         * Gets called when the element was triggered by the given event name.
         * Serializes the plugin element {@link $el} and sends it to the given url.
         * When the ajax request was successful, the {@link initModalSlider} will be called.
         *
         * @public
         * @event sendSerializedForm
         * @param {jQuery.Event} event
         */
        sendSerializedForm: function(event) {
            var me = this,
                $customProductsForm = $(me.opts.customProductFormSelector),
                parentArguments = arguments,
                optionManager = $customProductsForm.data(me.opts.optionManagerPluginName),
                hiddenInput = $customProductsForm.find(me.opts.hashInputSelector),
                formData;

            event.preventDefault();

            if (me.$buyBoxButton.find(me.opts.buyButtonSelector).hasClass('is--disabled')) {
                return;
            }

            if (!$customProductsForm.length) {
                me.originalFunction.apply(me, parentArguments);
                return;
            }

            $.each(optionManager.getAllOptions(), function() {
                this.validate();
            });

            if (!optionManager.checkValidity()) {
                optionManager.displayErrorOverview();
                $('body, html').css({
                    scrollTop: $(me.opts.errorOverviewSelector).offset().top
                });
                return;
            }
            optionManager.removeErrorOverview();

            if ($.isEmptyObject(optionManager._data)) {
                $('*[name=' + me.opts.hashInputName + ']').remove();
                me.originalFunction.apply(me, parentArguments);

                return;
            }

            formData = optionManager.getFormData();

            formData.append('templateId', $customProductsForm.attr(me.opts.templateIdAttr));
            formData.append('number', $customProductsForm.attr(me.opts.overviewNumberAttr));

            /**
            * Fires an ajax call to save the configuration as a hash value. It is necessary to do this because
            * the default add-to-basket-request is a jsonp request which can't send enough data caused by it's property
            * to send a GET request.
            */
            $.ajax({
                'type': 'POST',
                'url': optionManager.generateProtocolRelativeUrl($customProductsForm.attr(me.opts.customUrlAttr)),
                'data': formData,
                'processData': false,
                'contentType': false
            }).done(function(result) {
                window.history.replaceState(undefined, undefined, '#' + result.hash);

                if (hiddenInput) {
                    hiddenInput.remove();
                }

                $(me.opts.buyFormSelector).append(
                    [
                        '<input type="hidden" name="',
                        me.opts.hashInputName,
                        '" value="',
                        result.hash,
                        '" />'
                    ].join('')
                );
                me.originalFunction.apply(me, parentArguments);
            });
        }
    });

    // Plugin starter
    $(function() {
        $.subscribe('plugin/swAjaxVariant/onRequestData', function () {
            $('*[data-add-article="true"]').swagCustomProductsSwAddArticle();
        });

        $('.is--ctl-detail *[data-add-article="true"]').swagCustomProductsSwAddArticle();
    });
})(jQuery, window);
