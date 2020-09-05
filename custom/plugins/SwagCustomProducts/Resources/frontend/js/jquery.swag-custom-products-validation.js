/* global Handlebars */
/* global Modernizr */

/**
 * Emulate FormData for some browsers
 * MIT License
 * (c) 2010 François de Metz
 */
;(function(w) {
    if (w.FormData) {
        return;
    }

    function FormData() {
        this.fake = true;
        this.boundary = '--------FormData' + Math.random();
        this._fields = [];
    }

    FormData.prototype.append = function(key, value) {
        this._fields.push([key, value]);
    };

    FormData.prototype.toString = function() {
        var boundary = this.boundary,
            body = '';

        this._fields.forEach(function(field) {
            body += '--' + boundary + '\r\n';
            // file upload
            if (field[1].name) {
                var file = field[1];
                body += 'Content-Disposition: form-data; name="' + field[0] + '"; filename="' + file.name + '"\r\n';
                body += 'Content-Type: ' + file.type + '\r\n\r\n';
                body += file.getAsBinary() + '\r\n';
            } else {
                body += 'Content-Disposition: form-data; name="' + field[0] + '";\r\n\r\n';
                body += field[1] + '\r\n';
            }
        });
        body += '--' + boundary + '--';
        return body;
    };

    w.FormData = FormData;
})(window);

;(function($, Handlebars, Modernizr, window, undefined) {
    'use strict';

    /**
     * Constructor for the OptionManager singleton.
     *
     * @param {jQuery} $el
     * @param {Object} opts
     * @returns {OptionManager}
     * @constructor
     */
    function OptionManager($el, opts) {
        var me = this;

        me.$el = $el;
        me.opts = opts;
        me.optionCollection = [];
        me.$body = $('body');

        me.$resetConfigurationButton = $el.find(opts.resetConfigurationSelector);
        me.hasHistorySupport = Modernizr.history;

        me._articleId = window.parseInt(me.$el.attr('data-overview-articleid'), 10);
        me._orderNumber = me.$el.attr('data-overview-number');
        me._url = me.generateProtocolRelativeUrl(me.$el.attr('data-overview-url'));
        me._configurationUrl = me.$el.attr('data-get-configuration-url');
        me._format = me.$el.attr('data-overview-format');
        me._eventName = '.swagCustomProductOptionManager';
        me._starSnippet = me.$el.attr('data-star-snippet');
        me._originalHref = window.location.href;

        me.overviewTpl = Handlebars.compile($(me.opts.tplSelector).html());

        me.$displayContainer = $(me.opts.displayContainerSelector);
        me.$errorOverviewContainer = $(me.opts.errorOverviewSelector);
        me.$errorOverviewContainer.hide();

        me.$nextButton = $(me.opts.nextButtonSelector);

        me.$resetConfigurationButton.on('click' + me._eventName, $.proxy(me.onResetConfiguration, me));
        me.$el.on('change' + me._eventName, $.proxy(me.onFormChange, me));

        me.forceCustomerCheckbox = new ForceCustomerCheckbox(me.$el, me.opts);

        me._data = {};

        if (window.location.hash && window.location.hash.length) {
            var hash = window.location.hash.substring(1);

            if (window.CSRF.checkToken()) {
                me.getConfigurationFromHash(hash);
            } else {
                $.subscribe('plugin/swCsrfProtection/init', function() {
                    me.getConfigurationFromHash(hash);
                });
            }
        }

        $.publish('plugin/OptionManager/init', [me]);

        /**
         * Registers a new Handlebars helper which can be used throughout the application. The helper formats a
         * float to a currency. Please keep in mind the helper "only" supports "en-US" and "de-DE" currency formats
         * and number systems.
         */
        Handlebars.registerHelper('formatPrice', function(value) {
            var currencyFormat = me._format;
            value = Math.round(value * 100) / 100;
            value = value.toFixed(2);

            if (value === '0.00') {
                return '';
            }

            if (currencyFormat.indexOf('0.00') > -1) {
                value = currencyFormat.replace('0.00', value);
            } else {
                value = value.replace('.', ',');
                value = currencyFormat.replace('0,00', value);
            }

            value += me._starSnippet;

            return new Handlebars.SafeString(value);
        });

        $.publish('plugin/OptionManager/afterRegisterFormatPriceHandlebarsHelper', [me]);

        return me;
    }

    OptionManager.prototype = {

        /** @boolean Was the price overview loaded for the first time? **/
        _initial: true,

        /** @string|null Configuration hash */
        _hash: null,

        /** @xmlHttpRequest|null */
        ajaxCall: null,

        /**
         * Returns HTML5 form data which will be used to request the overview and add the configuration to the basket.
         * @returns {FormData}
         */
        getFormData: function() {
            var me = this,
                formData = new window.FormData();

            $.each(me._data, function(key, val) {
                formData.append(key, val);
            });

            $.publish('plugin/OptionManager/getFormData', [me, formData]);

            return formData;
        },

        /**
         * Event listener method which will be fired when the user changes one of the form elements
         * in the custom products form.
         *
         * @returns void
         */
        onFormChange: function(event) {
            var me = this;

            me.removeErrorOverview();
            $.each(me.optionCollection, function() {
                var option = this,
                    val = option.getValue(),
                    fieldName = option.getFieldName();

                if (fieldName.slice(-2) === '[]') {
                    fieldName = fieldName.substr(0, fieldName.length - 2);
                }

                option._value = val;

                if (val === undefined) {
                    delete me._data[fieldName];
                }

                if (typeof (val) === 'string') {
                    if (val.length) {
                        me._data[fieldName] = val;
                    } else {
                        delete me._data[fieldName];
                    }
                    return;
                }

                if (typeof (val) === 'number') {
                    if (!isNaN(val)) {
                        me._data[fieldName] = val;
                    } else {
                        delete me._data[fieldName];
                    }
                    return;
                }

                // Checkbox etc...
                if ($.isArray(val)) {
                    if (val[0] && val[0].length > 0) {
                        // Try to cast values to number
                        $.each(val, function(i, item) {
                            var float = window.parseFloat(item);
                            val[i] = (isNaN(float) ? item : float);
                        });

                        me._data[fieldName] = val;
                    } else {
                        delete me._data[fieldName];
                    }
                }
            });

            if ($(event.target).attr('id') !== 'custom-products--checkbox-confirm-input') {
                me.forceCustomerCheckbox.reset();
            }

            me.requestOverviewPanel();
            if (Object.keys(me._data).length >= 1) {
                me.forceCustomerCheckbox.show();
                return;
            } else {
                me.setHashToHistory('');
            }
            me.forceCustomerCheckbox.hide();

            $.publish('plugin/OptionManager/getFormData', [me, me._data, (Object.keys(me._data).length >= 1)]);
        },

        /**
         * Buffers the request to limit the amount of HTTP requests which will be fired to the server.
         * This is necessary cause the user can spam the form fields.
         *
         * @returns void
         */
        requestOverviewPanel: function() {
            var me = this;

            if (me.timeout) {
                clearTimeout(me.timeout);
            }

            $.publish('plugin/OptionManager/requestOverviewPanel', [me]);

            me.timeout = setTimeout(function() {
                me.triggerRequest(true);
            }, me.opts.requestBuffer);
        },

        /**
         * Triggers the actual AJAX request to get the overview data from
         * the server side. The request will be sent as an XMLHttpRequest with
         * FormData, so it's possible to send files too.
         *
         * @param {Boolean=} force
         * @returns void
         */
        triggerRequest: function(force) {
            var me = this, quantity,
                formData = me.getFormData();

            force = force || false;

            if (!force && !Object.keys(me._data).length) {
                return;
            }

            $.publish('plugin/OptionManager/triggerRequest', [me, formData, me._articleId]);

            quantity = $(me.opts.quantityBoxSelector).val();
            if (!quantity) {
                quantity = 1;
            }

            if (me.ajaxCall) {
                me.ajaxCall.abort();
                me.ajaxCall = null;
            }

            me.ajaxCall = $.ajax({
                url: me._url + '?sArticle=' + me._articleId + '&number=' + me._orderNumber + '&sQuantity=' + quantity,
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST'
            }).done(
                $.proxy(me.onOverviewPanelSuccess, me)
            ).fail(
                $.proxy(me.onOverviewPanelFail, me)
            );
        },

        /**
         * Event listener method which will be fired when the AJAX request was
         * processed correctly.
         *
         * It will unlock the {@link $nextButton} and renders the overview.
         *
         * @param {Object} response - JSON object of the response
         */
        onOverviewPanelSuccess: function(response) {
            var me = this,
                responseData = response.data,
                output;

            me.$nextButton.removeAttr('disabled');

            me.$displayContainer.empty();

            if (me.shouldShowOverview(responseData)) {
                output = me.overviewTpl(responseData);
                me.$displayContainer.html(output);
            }

            if (!me._initial) {
                me.forceCustomerCheckbox[(Object.keys(me._data).length >= 1) ? 'show' : 'hide']();
            }

            me.setHashToHistory(response.hash);
            me.setHashToConfiguratorForm(response.hash);
            me._initial = false;
            me.ajaxCall = null;

            $.publish('plugin/OptionManager/onOverviewPanelSuccess', [me, response]);
        },

        /**
         * Adds the configuration hash to the action link of the configurator form.
         *
         * @param {String} hash
         */
        setHashToConfiguratorForm: function(hash) {
            var me = this,
                configuratorForm = $(me.opts.configuratorFormSelector),
                dropDownSelection = $(me.opts.articleDetailSelector),
                isAjaxVariantSelection = dropDownSelection.attr('data-ajax-variants-container'),
                actionLink = configuratorForm.attr('action'),
                url = [];

            if (actionLink) {
                url = actionLink.split('#');
            }

            if (isAjaxVariantSelection) {
                return;
            }

            if (!hash) {
                configuratorForm.attr('action', url);
                return;
            }

            configuratorForm.attr('action', url[0] + '#' + hash);
        },

        /**
         * Event listener method which will be fired when the AJAX request
         * was faulty. Either the HTTP status code wasn't 200/304 or the JSON
         * data is malformed.
         *
         * It will unlock the {@link $nextButton} and is more like a template method for
         * third party developers which wanna show a custom alert to the user.
         */
        onOverviewPanelFail: function() {
            var me = this;

            me.$nextButton.removeAttr('disabled');
            me.ajaxCall = null;

            $.publish('plugin/OptionManager/onOverviewPanelFail', [me]);
        },

        /**
         * Decides whether or not the overview should be shown after triggering a request.
         *
         * @param {Array} responseData - The response-data of the overview-calculation-request
         * @returns {Boolean}
         */
        shouldShowOverview: function(responseData) {
            return (responseData.surcharges && responseData.surcharges.length > 0) || (responseData.onceprices && responseData.onceprices.length > 0);
        },

        /**
         * Requests the configuration of the custom product based on the hash.
         *
         * @param {String} hash
         */
        getConfigurationFromHash: function(hash) {
            var me = this;

            me._hash = hash;

            $.publish('plugin/OptionManager/getConfigurationFromHash', [me, hash]);

            $.ajax({
                url: me.generateProtocolRelativeUrl(me._configurationUrl),
                data: {
                    hash: hash
                },
                method: 'GET',
                success: $.proxy(me.onGetConfiguration, me)
            });
        },

        /**
         * Callback method when the request to get the configuration is done.
         *
         * @param {Object} response
         */
        onGetConfiguration: function(response) {
            var me = this,
                options = me.getAllOptions(),
                configuration = response.configuration;

            $.each(options, function() {
                var option = this,
                    fieldName = option.getFieldName(),
                    val;

                // Remove the group symbol behind the field name
                if (fieldName.slice(-2) === '[]') {
                    fieldName = fieldName.substr(0, fieldName.length - 2);
                }

                if (!configuration || !configuration.hasOwnProperty(fieldName)) {
                    return;
                }

                val = configuration[fieldName];
                option.reset(val);
            });

            if (me.forceCustomerCheckbox.shouldCheck(me._data)) {
                me.forceCustomerCheckbox.show();
            }
            me.triggerRequest(false);

            $.publish('plugin/OptionManager/onGetConfiguration', [me, response, options, configuration]);
        },

        /**
         * Replace the hash for the configuration in the history of the user.
         *
         * @param {String} hash
         * @returns {OptionManager}
         */
        setHashToHistory: function(hash) {
            var me = this;

            window.history.replaceState(undefined, undefined, '#' + hash);

            me.setHashToConfiguratorForm(hash);
            me._hash = hash;

            $.publish('plugin/OptionManager/setHashToHistory', [this, hash]);

            return this;
        },

        /**
         * Event handler method which will be fired when the user clicks the reset configuration button.
         *
         * @param {jQuery.Event} event
         * @returns {OptionManager}
         */
        onResetConfiguration: function(event) {
            var me = this;

            event.preventDefault();

            // Reset internal data tracking
            $.each(me.optionCollection, function() {
                var option = this;

                option.reset();
                option.markLabel(false);
                option.markOption(false);
            });

            me.removeErrorOverview();
            if (me.forceCustomerCheckbox) {
                me.forceCustomerCheckbox.hide();
                me.forceCustomerCheckbox.reset();
            }

            if (me.hasHistorySupport) {
                window.history.replaceState('',
                    document.title,
                    me._originalHref
                );
            } else {
                // Workaround when the browser doesn't support the history object. Please note that this will make the
                // browser jumps to the beginning of the page.
                window.location.hash = '';
            }

            me.triggerRequest(true);

            $.publish('plugin/OptionManager/onResetConfiguration', [me, event]);

            return me;
        },

        /**
         * Returns a jQuery object of the reset configuration button.
         *
         * @returns {jQuery}
         */
        getResetConfigurationButton: function() {
            return this.$resetConfigurationButton;
        },

        /**
         * Adds new options to the manager.
         *
         * @param {Object|Array} option
         * @returns {OptionManager}
         */
        add: function(option) {
            var me = this;

            if ($.isArray(option)) {
                $.each(option, function() {
                    me.optionCollection.push(this);
                });

                return me;
            }

            me.optionCollection.push(option);

            $.publish('plugin/OptionManager/add', [me, option]);

            return me;
        },

        /**
         * Returns all registered options
         *
         * @returns {Option[]}
         */
        getAllOptions: function() {
            return this.optionCollection;
        },

        /**
         * Returns all required options
         *
         * @returns {Option[]}
         */
        getAllRequiredFields: function() {
            var me = this,
                options = me.getAllOptions(),
                requiredOptions = [];

            $.each(options, function(i, option) {
                var validator = option.getValidator();

                if (!validator.isRequired()) {
                    return;
                }

                requiredOptions.push(option);
            });

            return requiredOptions;
        },

        /**
         * Remove all items in the passed array from the collection.
         *
         * @param {Array} opts
         */
        removeMulti: function(opts) {
            var i, iLen;

            opts = [].concat(opts);
            iLen = opts.length;

            for (i = 0; i < iLen; i++) {
                this.remove(opts[i]);
            }

            $.publish('plugin/OptionManager/removeMulti', [this, opts]);
            return this;
        },

        /**
         * Remove an item from a specified index in the collection.
         *
         * @param index
         * @returns {OptionManager}
         */
        removeAt: function(index) {
            var me = this;

            me.optionCollection.splice(index, 1);

            $.publish('plugin/OptionManager/removeAt', [me, index]);
            return me;
        },

        /**
         * Checks if the custom products form is valid and all options are passing the validitiy check.
         *
         * @returns {boolean}
         */
        checkValidity: function(ignoreForceCustomer) {
            var me = this,
                isValid = true,
                options = me.getAllOptions();

            ignoreForceCustomer = ignoreForceCustomer || false;

            $.each(options, function() {
                var option = this;

                if (!option.getValidator().isValid()) {
                    isValid = false;
                    return false;
                }
            });

            // Customer checkbox
            if (!ignoreForceCustomer && options.length && me.forceCustomerCheckbox.shouldCheck(me._data) && isValid) {
                isValid = me.forceCustomerCheckbox.isValid();
            }

            $.publish('plugin/OptionManager/checkValidity', [me, isValid, options]);
            return isValid;
        },

        /**
         * Provides all error messages from the invalid options.
         *
         * @returns {Array}
         */
        getAllErrorMessages: function() {
            var me = this,
                options = me.getAllOptions(),
                messages = [];

            $.each(options, function() {
                var validator = this.getValidator();

                if (!validator.isValid()) {
                    messages.push({
                        errorMessage: validator.getMessage(),
                        optionName: this.getLabelText(),
                        id: this.getLabelId()
                    });
                }
            });

            if (options.length && me.forceCustomerCheckbox.shouldCheck(me._data)) {
                messages.push({
                    errorMessage: me.forceCustomerCheckbox.getErrorMessage(),
                    id: '.panel--footer'
                });
            }

            $.publish('plugin/OptionManager/getAllErrorMessages', [me, messages]);

            return messages;
        },

        /**
         * Display all errors over the basket button to inform the user that something went wrong.
         *
         * @returns void
         */
        displayErrorOverview: function() {
            var me = this,
                messages = me.getAllErrorMessages(),
                tpl = Handlebars.compile(me.opts.errorOverviewTpl);

            me.$errorOverviewContainer.empty().html(
                tpl(
                    {messages: messages}
                )
            );

            me.$errorOverviewContainer.show();

            if (me.forceCustomerCheckbox.shouldCheck(me._data)) {
                me.forceCustomerCheckbox.markLabel(true);
                me.forceCustomerCheckbox.show();
            }

            $.publish('plugin/OptionManager/displayErrorOverview', [me, messages]);
        },

        /**
         * Removes the content of the error overview container.
         *
         * @returns void
         */
        removeErrorOverview: function() {
            var me = this;

            me.$errorOverviewContainer.empty();
            me.$errorOverviewContainer.hide();

            if (me.forceCustomerCheckbox.shouldCheck(me._data)) {
                me.forceCustomerCheckbox.markLabel(false);
                me.forceCustomerCheckbox.hide();
            }

            $.publish('plugin/OptionManager/removeErrorOverview', [me]);
        },

        /**
         * Helper function to generate protocol relative urls.
         *
         * @param {string} url
         * @return {string}
         */
        generateProtocolRelativeUrl: function(url) {
            // Relative paths will automatically use the correct protocol, i.e. /shop/SwagCustomProducts/example
            if ((url.indexOf('https') === -1) && (url.indexOf('http') === -1)) {
                return url;
            }

            switch (window.location.protocol) {
                case 'https:':
                    return url.replace('http://', '//');
                case 'http:':
                    return url.replace('https://', '//');
                default:
                    throw new Error('Passed an invalid url, got ' + url);
            }
        },

        /**
         * Destroy method of the plugin.
         * Removes attached event listener.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me.$resetConfigurationButton.off('click' + me._eventName);
            me.$el.off('change' + me._eventName);
        }
    };

    /**
     * ForceCustomerCheckbox which provides the interface to check if the user has to
     * check a checkbox. If we force the user to check it, the buy button will return an error message
     * when the user hasn't checked the box.
     *
     * @param {jQuery} $el
     * @param {Object} opts
     * @returns {ForceCustomerCheckbox}
     * @constructor
     */
    function ForceCustomerCheckbox($el, opts) {
        var me = this;

        me.$el = $el;
        me.opts = opts;

        me.checkInput = me.$el.attr('data-validate-confirm-input');
        me.checkInput = (me.checkInput === 'true');
        me.checkInputEl = me.$el.find('*[data-validate-input]');
        me.$label = me.checkInputEl.parents('label');
        me.$box = me.$el.find('.panel--footer');

        me.message = me.checkInputEl.attr('data-validate-message');

        $.publish('plugin/ForceCustomerCheckbox/init', [me]);
        return me;
    }

    ForceCustomerCheckbox.prototype = {

        /**
         * Should the checkbox be checked.
         * @returns {boolean}
         */
        shouldCheck: function(data) {
            return this.checkInput && Object.keys(data).length >= 1;
        },

        /**
         * Returns the element which should be validated.
         * @returns {jQuery}
         */
        getValidateElement: function() {
            return this.$el.find('*[data-validate-input]');
        },

        /**
         * Is the checkbox valid?
         * @returns {boolean}
         */
        isValid: function() {
            var me = this,
                $el = me.getValidateElement();

            return $el.is(':checked');
        },

        /**
         * Get label element.
         * @returns {jQuery}
         */
        getLabel: function() {
            return this.$label;
        },

        /**
         * Marks the checkbox if the toggle is set to true.
         * @param toggle
         * @returns {boolean}
         */
        markLabel: function(toggle) {
            this.$label[(toggle === true) ? 'addClass' : 'removeClass']('has--error');

            return true;
        },

        /**
         * Shows the checkbox.
         * @returns void
         */
        show: function() {
            this.$box.removeClass('is--hidden');

            $.publish('plugin/ForceCustomerCheckbox/show', [this]);

            return this;
        },

        /**
         * Hides the checkbox.
         * @returns void
         */
        hide: function() {
            this.$box.addClass('is--hidden');

            $.publish('plugin/ForceCustomerCheckbox/hide', [this]);

            return this;
        },

        /**
         * Gets the error message for the checkbox.
         * @returns {String}
         */
        getErrorMessage: function() {
            return this.message;
        },

        /**
         * Resets the checkbox.
         * @returns void
         */
        reset: function() {
            var me = this;

            $.publish('plugin/ForceCustomerCheckbox/reset', [this]);

            me.getValidateElement().prop('checked', me.getValidateElement().prop('defaultChecked'));
        }
    };

    /**
     * Reflects one option in a custom product. It handles all actions a option can have.
     *
     * @param {jQuery} $el
     * @param {Object} optionManager
     * @param {Object} opts
     * @returns {Option}
     * @constructor
     */
    function Option($el, optionManager, opts) {
        var me = this;

        me.opts = opts;
        me.$el = $el;

        me.$label = me.$el.find(me.opts.labelSelector);
        me.required = me.$el.is(me.opts.requiredSelector);

        me.optionManager = optionManager;

        me.$resetButton = me.$el.find(me.opts.resetOptionSelector);
        me.$parent = me.$el.find(me.opts.parentSelector);

        if (!me.$parent.length) {
            me.$parent = me.$el.find(me.opts.parentSelectorWizard);
        }

        me.$resetButton.on('click.swagCustomProductOption', $.proxy(me.onResetButton, me));

        me.$field = me.$el.find(['*[data-field="true"]', '*[data-group-field="true"]'].join(','));

        me.groupField = me.$el.has('*[data-group-field="true"]').length > 0;
        me.validator = new Validator(me, me.$field, opts);

        me.$errorEl = $(me.validator.getMessageHTML());

        me.$el.data('swagCustomProductOption', me);

        me._value = me.getValue();

        me.isWizardOption = me.$el.hasClass('is--wizard');

        $.publish('plugin/Option/init', [me]);

        return this;
    }

    Option.prototype = {

        /**
         * Event listener method which will be fired when the user presses the reset button inside a option.
         *
         * The method resets the form field, removes the invalid flag from the label and removes the error message.
         *
         * @param {jQuery.Event} event
         */
        onResetButton: function(event) {
            var me = this;

            event.preventDefault();

            me.reset();
            me.markLabel(false);
            me.markOption(false);

            $.publish('plugin/Option/onResetButton', [me, event]);
        },

        /**
         * Returns the instance of the reset button.
         *
         * @returns {jQuery}
         */
        getResetButton: function() {
            return this.$resetButton;
        },

        /**
         * Validates the option, marks the label as invalid and shows a error message inside the option if the field
         * isn't valid.
         *
         * @returns {Option}
         */
        validate: function() {
            var me = this,
                validator = me.getValidator(),
                isValid = validator.isValid();

            me.markLabel(!isValid);
            me.markOption(!isValid);

            $.publish('plugin/Option/validate', [me, validator, isValid]);

            return me;
        },

        /**
         * Marks the label as either valid or invalid.
         *
         * @param {Boolean} toggle - True to mark it as invalid
         * @returns {Option}
         */
        markLabel: function(toggle) {
            var me = this;

            if (me.isWizardOption && !me.hasOwnProperty('wasSeen')) {
                return me;
            }

            me.getLabelElement()[(toggle === true) ? 'addClass' : 'removeClass'](me.opts.labelErrorClass);

            $.publish('plugin/Option/markLabel', [me, toggle]);

            return me;
        },

        /**
         * Shows a error message or removes it depending on the parameter.
         *
         * @param {Boolean} toggle - True to show a error message, otherwise remove the error message
         * @returns {Option}
         */
        markOption: function(toggle) {
            var me = this;

            if (me.isWizardOption && !me.hasOwnProperty('wasSeen')) {
                return me;
            }

            // Remove any previous error message cause we don't know if the error message is still the
            // one we wanna display.
            me.$parent.find(me.opts.errorContainerSelector).addClass('is--hidden');
            me.$errorEl = $(me.validator.getMessageHTML());

            if (toggle) {
                me.$parent.prepend(me.$errorEl);

                $.publish('plugin/Option/markOption', [me, toggle]);

                return me;
            }

            $.publish('plugin/Option/markOption', [me, toggle]);

            return me;
        },

        /**
         * Returns the label element of the option.
         *
         * @returns {jQuery}
         */
        getLabelElement: function() {
            return this.$label;
        },

        /**
         * Returns the label text of the option.
         *
         * @returns {String|null}
         */
        getLabelText: function() {
            var label = this.getLabelElement();

            return label.attr(this.opts.labelTextName);
        },

        /**
         * Returns the label id for the option.
         *
         * @returns {String}
         */
        getLabelId: function() {
            var label = this.getLabelElement();

            return '#' + label.attr('for');
        },

        /**
         * Returns the element of the option.
         *
         * @returns {jQuery}
         */
        getElement: function() {
            return this.$el;
        },

        /**
         * Returns the name of the field.
         * @returns {String}
         */
        getFieldName: function() {
            var me = this,
                name = me.$field.attr('name'),
                parentCt;

            if (name !== undefined && name !== '') {
                return name;
            }

            $.each(me.$field.find(':input'), function() {
                var field = $(this);

                name = field.attr('name');
                if (name !== undefined && name !== '') {
                    return false;
                }
            });

            if (name !== undefined && name !== '') {
                return name;
            }

            // used for date and time fields
            parentCt = me.$field.parents('.custom-products--date');
            name = parentCt.find('input[type=hidden]').attr('name');

            if (name === undefined) {
                return '';
            }

            return name;
        },

        /**
         * Returns the either the field or the field container (when we're dealing with a {@link isGroupField}).
         *
         * @returns {jQuery}
         */
        getField: function() {
            return this.$field;
        },

        /**
         * Returns the option validator.
         *
         * @returns {Validator}
         */
        getValidator: function() {
            return this.validator;
        },

        /**
         * Returns true when we're dealing with a option with multiple fields e.g. checkbox group.
         *
         * @returns {Boolean}
         */
        isGroupField: function() {
            return this.groupField;
        },

        /**
         * Returns the value of the {@link getField}.
         *
         * @returns {Number|String}
         */
        getValue: function() {
            var me = this,
                styleRemover = /style="[a-zA-Z-:0-9.&;\s]*"*/g,
                val;

            if (me.isGroupField()) {
                return me.$field.find(':checked').map(function() {
                    return $(this).val();
                }).get();
            }

            if (me.$field.is('select')) {
                return me.$field.find(':selected').map(function() {
                    return $(this).val();
                }).get();
            }

            val = me.$field.val();

            // Escape HTML from wysiwyg editor
            if (me.$field.hasClass(me.opts.wysiwygEditorClass)) {
                val = $('<div/>').text(val).html();
                // Replace all style elements because the inputFilter filters out the whole value
                val = val.replace(styleRemover, '');

                return val;
            }

            if (me.$field.is('[type="number"]')) {
                val = window.parseFloat(val);
                return val;
            }

            if (me.$field.is('[type="file"]') && me.$field.data('data-uploadResponse')) {
                return me.$field.data('data-uploadResponse');
            }

            if (val.length <= 0) {
                return undefined;
            }

            return val;
        },

        /**
         * Resets the form fields which are in a option. The method loops through each input element, separates them
         * into types and supports all of our plugins.
         *
         * @returns {Option}
         */
        reset: function(resetValue) {
            var me = this,
                inputs = me.$el.find(':input');

            /**
             * Resets text field elements to it's initial state.
             *
             * @param {jQuery} $el
             * @param {String} defaultVal
             * @returns {boolean}
             */
            var resetTextField = function($el, defaultVal) {
                var plugin = $el.data('plugin_swDatePicker');
                $el.val(defaultVal);

                if (typeof (plugin) === 'object') {
                    plugin.flatpickr.set(defaultVal);
                }

                $.publish('plugin/Option/resetTextField', [me, $el, defaultVal]);

                return true;
            };

            /**
             * Resets textarea elements to it's initial state.
             *
             * @param {jQuery} $el
             * @param {String} defaultVal
             * @returns {boolean}
             */
            var resetTextarea = function($el, defaultVal) {
                var plugin = $el.data('plugin_swagCustomProductsWysiwyg');

                $el.val(defaultVal);

                if (typeof (plugin) !== 'object') {
                    $.publish('plugin/Option/resetTextarea', [me, $el, defaultVal]);
                    return;
                }
                plugin.setValue(defaultVal);

                $.publish('plugin/Option/resetTextarea', [me, $el, defaultVal]);
                return true;
            };

            /**
             * Resets checkbox & radiobox elements to it's initial state.
             *
             * @param {jQuery} $el
             * @param defaultVal
             * @returns {boolean}
             */
            var resetCheckboxRadioBox = function($el, defaultVal) {
                if (defaultVal) {
                    var splitted = defaultVal.split(',');

                    if (splitted.length > 1) {
                        defaultVal = splitted;
                    }

                    if (!$.isArray(defaultVal)) {
                        if ($el.val() === defaultVal) {
                            $el.prop('checked', true);
                        }
                    } else {
                        $.each(defaultVal, function() {
                            var val = this;

                            if ($el.val() === val) {
                                $el.prop('checked', true);
                            }
                        });
                    }

                    $.publish('plugin/Option/resetCheckboxRadioBox', [me, $el, defaultVal]);
                    return true;
                }

                $el.prop('checked', $el.prop('defaultChecked'));
                return true;
            };

            var resetFileField = function($el) {
                var $wrapper = $el.parent(),
                    plugin = $wrapper.data('plugin_swagCustomProductsUpload');

                plugin.reset(resetValue);

                $.publish('plugin/Option/resetFileField', [me, $el]);
            };

            /**
             * Resets text field elements to it's initial state.
             *
             * @param {jQuery} $el
             * @param defaultVal
             * @returns {boolean}
             */
            var resetSelectBox = function($el, defaultVal) {
                var multiple = $el.attr('multiple'),
                    splitted;

                multiple = multiple !== undefined;
                defaultVal = defaultVal || '';
                splitted = defaultVal.split(',');

                // Let's check if we have a default selected value
                if (!defaultVal.length) {
                    $.each($el.find('option'), function() {
                        var $option = $(this);

                        // We found the default selected value, processed with the rest
                        if ($option.prop('defaultSelected')) {
                            defaultVal = $option.val();
                            return false;
                        }
                    });
                }

                // So the browser hasn't default selected a entry, we can just reset the select field
                // and jump out
                if (!defaultVal && multiple) {
                    $el.val('');

                    $.publish('plugin/Option/resetSelectBox', [me, $el, defaultVal, multiple]);
                    return true;
                }

                // The default value was an array with comma separates values
                if (splitted.length > 1) {
                    defaultVal = splitted;
                }

                // If we have a default value but it's not an array, we cast it here
                if (!$.isArray(defaultVal)) {
                    defaultVal = [defaultVal];
                }

                if (multiple) {
                    $.each($el.find('option'), function(i, el) {
                        var $option = $(el);
                        $option.prop('selected', false);
                    });
                }

                // The actual logic, loop through the default values, find the option tag which
                // matches the value and select it
                $.each(defaultVal, function(i, val) {
                    var $option = $el.find('option[value="' + val + '"]');

                    if ($option.length) {
                        $option.prop('selected', 'selected');
                    }
                });

                $.publish('plugin/Option/resetSelectBox', [me, $el, defaultVal, multiple]);

                return true;
            };

            $.each(inputs, function() {
                var $el = $(this),
                    type = $el.attr('type'),
                    defaultVal = $el.attr('data-default-value') || '';

                if (resetValue) {
                    defaultVal = resetValue;
                }

                if (!type || !type.length) {
                    type = $el.prop('tagName').toLowerCase();
                }

                if (['file'].indexOf(type) !== -1) {
                    resetFileField($el);
                }

                if (['hidden', 'text', 'email', 'search', 'tel', 'number', 'time', 'date', 'password'].indexOf(type) !== -1) {
                    resetTextField($el, defaultVal);
                }

                if (['textarea'].indexOf(type) !== -1) {
                    resetTextarea($el, defaultVal);
                }

                if (['radio', 'checkbox'].indexOf(type) !== -1) {
                    $el.prop('checked', '');
                    resetCheckboxRadioBox($el, defaultVal);
                }

                if (['select'].indexOf(type) !== -1) {
                    resetSelectBox($el, defaultVal);
                }
                $el.trigger('change');
            });

            $.publish('plugin/Option/reset', [me, resetValue]);

            me._value = me.getValue();

            return me;
        }
    };

    /**
     * Validator for the custom product option.
     *
     * @param {Option} option
     * @param {jQuery} field
     * @param {opts} opts
     * @returns {Validator}
     * @constructor
     */
    function Validator(option, field, opts) {
        var me = this;

        me.opts = opts;
        me.option = option;
        me.$field = field;
        me.msg = me.$field.attr(me.opts.messageAttribute);
        me.tpl = Handlebars.compile(me.opts.errorTpl);

        me.required = me.$field.is([me.opts.singleFieldSelector, me.opts.groupFieldSelector].join(','));

        $.publish('plugin/Validator/init', [me]);

        return this;
    }

    Validator.prototype = {

        isRequired: function() {
            return this.required;
        },

        /**
         * Returns the option which uses the validator.
         *
         * @returns {Option}
         */
        getOption: function() {
            return this.option;
        },

        /**
         * Checks if the field is valid. This method is pretty simple because it only checks if the user has entered
         * a value.
         *
         * @returns {Boolean}
         */
        isValid: function() {
            var me = this,
                option = this.getOption(),
                val = option.getValue(),
                isValid;

            // Wysiwyg validation
            if (option.$field.hasClass(me.opts.wysiwygEditorClass) && !val) {
                isValid = true;

                if (me.isRequired()) {
                    me.msg = option.$field.attr(me.opts.messageAttribute);
                    isValid = false;
                }

                $.publish('plugin/Validator/isValid', [me, isValid, option, val]);
                return isValid;
            }

            // File upload validation
            if (option.$field.hasClass('upload--field') && !val) {
                isValid = true;

                if (me.isRequired()) {
                    me.msg = option.$field.attr('data-validate-message-required');
                    isValid = false;
                }

                $.publish('plugin/Validator/isValid', [me, isValid, option, val]);
                return isValid;
            }

            // Number field
            if (typeof val === 'number' && me.isValidNumber(val)) {
                $.publish('plugin/Validator/isValid', [me, false, option, val]);

                return false;
            }

            if ($.isNumeric(val)) {
                var $field = option.$field;

                if (!me.isNumberInRange($field, val)) {
                    $.publish('plugin/Validator/isValid', [me, false, option, val]);
                    return false;
                }

                // Reset the message back to the default value.
                me.msg = $field.attr(me.opts.messageAttribute);

                $.publish('plugin/Validator/isValid', [me, true, option, val]);
                return true;
            }

            if ($.isArray(val)) {
                isValid = me.isValidArray(val);
                $.publish('plugin/Validator/isValid', [me, isValid, option, val]);
                return isValid;
            }

            // If we have a value (string or array) and the value has a length, it's valid
            if (val) {
                isValid = val.length > 0;
                $.publish('plugin/Validator/isValid', [me, isValid, option, val]);
                return isValid;
            }

            $.publish('plugin/Validator/isValid', [me, !me.isRequired(), option, val]);
            return !me.isRequired();
        },

        /**
         * Returns the field which uses the validator.
         * @returns {jQuery}
         */
        getField: function() {
            return this.$field;
        },

        /**
         * Returns the error message of the option.
         *
         * @returns {String}
         */
        getMessage: function() {
            return this.msg;
        },

        /**
         * Returns the rendered error message to insert it into the DOM.
         *
         * @returns {String}
         */
        getMessageHTML: function() {
            return this.tpl({message: this.getMessage(), showIcon: false});
        },

        /**
         * Checks if a value from type 'number' is valid.
         *
         * @param {*} val
         * @returns {Boolean}
         */
        isValidNumber: function(val) {
            var me = this;
            return isNaN(val) && me.isRequired();
        },

        /**
         * Checks if a numeric value is in the range of the given option-settings.
         *
         * @param {jQuery} $field
         * @param {*} val
         * @returns {Boolean}
         */
        isNumberInRange: function($field, val) {
            var me = this,
                min = $field.attr('min'),
                max = $field.attr('max'),
                step = $field.attr('step');

            min = window.parseFloat(min);
            max = window.parseFloat(max);
            step = window.parseFloat(step);

            // Check lower bound
            if (!isNaN(min) && val < min) {
                // We have custom error messages for the different cases.
                me.msg = $field.attr('data-validate-message-min');
                return false;
            }

            // Check upper bound
            if (!isNaN(max) && val > max) {
                me.msg = $field.attr('data-validate-message-max');
                return false;
            }

            // Return true if there is no lower bound and the value is valid
            if (isNaN(min) && !isNaN(val)) {
                return true;
            }

            // Check for compliance with defined interval
            if (!me.isInIntervalSequence(val, min, step)) {
                me.msg = $field.attr('data-validate-message-step');
                return false;
            }

            return true;
        },

        /**
         * Check if a numeric value lies on an interval defined by a start and a step value
         *
         * @param {float} val
         * @param {int} start
         * @param {int} step
         * @returns {boolean}
         */
        isInIntervalSequence: function(val, start, step) {
            var precisionFactor = 1000000;
            step = step || 1;

            if (isNaN(val) || isNaN(start)) {
                return false;
            }

            // Multiply values before remainder operation to mitigate risk of floating-point-precision errors
            // Original conditional looks like "(val - start) % step === 0"
            return Math.round((val - start) * precisionFactor) % Math.round((step * precisionFactor)) / precisionFactor === 0;
        },

        /**
         * Checks if an array is actually valid.
         *
         * @param {Array} val
         * @returns {Boolean}
         */
        isValidArray: function(val) {
            var me = this;

            if (val.length > 1) {
                return true;
            }

            if (val.length > 0) {
                return val[0].length > 0 || !me.isRequired();
            }

            return !me.isRequired();
        }
    };

    $.plugin('swagCustomProductsOptionManager', {

        /** @object Default plugin configuration */
        defaults: {
            formSelector: '.custom-products--form',

            optionSelector: '.custom-products--option',
            requiredSelector: '[data-swag-custom-products-required="true"]',

            singleFieldSelector: '[data-validate="true"]',
            groupFieldSelector: '[data-validate-group="true"]',
            parentSelector: '.custom-product--option-wrapper',
            parentSelectorWizard: '.custom-product--option-wrapper-wizard',
            labelSelector: '.custom-products--label',
            labelTextName: 'data-label',
            messageAttribute: 'data-validate-message',
            labelErrorClass: 'has--validation-error',

            resetOptionSelector: '[data-custom-products-reset="true"]',
            quantityBoxSelector: '#sQuantity',
            resetConfigurationSelector: '.custom-products--global-reset',

            nextButtonSelector: '.buybox--form .buybox--button',
            displayContainerSelector: '.custom-products--global-calculation-overview',
            tplSelector: '#overview-template',
            requestBuffer: 350,
            errorOverviewSelector: '.custom-products--global-error-overview',
            errorOverviewLinkSelector: '.custom-products--error-list a',
            scrollOffset: 115,
            errorContainerSelector: '.is--error',

            configuratorFormSelector: '.configurator--form',
            articleDetailSelector: '.content .product--details',

            wysiwygEditorClass: 'trumbowyg-textarea',

            quantitySelector: 'form[name="sAddToBasket"] .quantity--select',

            errorTpl: [
                '<div class="alert is--error is--rounded">',
                '{{#if showIcon}}',
                '<div class="alert--icon">',
                '<i class="icon--element icon--warning"></i>',
                '</div>',
                '{{/if}}',
                '<div class="alert--content">',
                '{{message}}',
                '</div>',
                '</div>'
            ].join(''),

            errorOverviewTpl: [
                '<div class="alert is--error">',
                '<div class="alert--icon">',
                '<i class="icon--element icon--warning"></i>',
                '</div>',

                '<div class="alert--content">',
                '<ul class="custom-products--error-list">',
                '{{#each messages}}',
                '<li class="error-list--item"><a href="{{id}}">{{#if optionName}}{{optionName}} - {{/if}}{{errorMessage}}</a></li>',
                '{{/each}}',
                '</ul>',
                '</div>',
                '</div>'
            ].join('')
        },

        /**
         * Initializes the plugin, sets up the necessary elements,
         * checks if all parameters are set and registers the event listeners.
         */
        init: function() {
            var me = this,
                $form = $(me.opts.formSelector);

            me.$form = $form;
            me.optionManager = new OptionManager(me.$el, me.opts);
            me.$options = me.$el.find(me.opts.optionSelector);
            me.$quantitySelect = $(me.opts.quantitySelector);

            me.$el.data('plugin_optionManager', me.optionManager);

            $(me.opts.errorOverviewSelector).on(me.getEventName('click'), me.opts.errorOverviewLinkSelector, $.proxy(me.onErrorMessageLink, me));

            $.each(me.$options, function() {
                me.optionManager.add(new Option($(this), me.optionManager, me.opts));
            });

            $form.change();

            me._on($form, 'keypress', $.proxy(me.onKeyPress, me));
            me._on(me.$quantitySelect, 'change', $.proxy(me.onQuantityChange, me));
        },

        /**
         * @param { object } event
         */
        onKeyPress: function(event) {
            var me = this,
                $target = $(event.target),
                isEditor = $target.has('trumbowyg-editor');

            // Suppress the enter key to avoid submitting the form the wrong way
            if (event.keyCode === 13) { // 13 = enter key
                if (!isEditor) {
                    event.preventDefault();
                }

                me.$form.change();
            }
        },

        /**
         * Event handler method which will be fired when the user clicks a link in the error overview.
         *
         * The method scrolls to the wrong option, so the user knows what he/she has to fill out.
         *
         * @param {jQuery.Event} event
         */
        onErrorMessageLink: function(event) {
            var me = this,
                href = $(event.target).attr('href'),
                $targetEl = $(href);

            event.preventDefault();

            $('body,html').animate({
                scrollTop: $targetEl.offset().top - me.opts.scrollOffset
            }, 800);

            $.publish('plugin/swagCustomProductsOptionManager/onErrorMessageLink', [me, href, event]);
        },

        /**
         * triggers a form change to update the option overview
         */
        onQuantityChange: function() {
            this.$form.change();
        },

        /**
         * Destroy method of the plugin.
         * Removes attached event listener.
         *
         * @returns void
         */
        destroy: function() {
            var me = this;

            me.optionManager.destroy();
            me._destroy();
        }
    });

    $(function() {
        StateManager.addPlugin('*[data-swag-custom-products-option-manager="true"]', 'swagCustomProductsOptionManager');

        var hash = null,
            $form = $('.custom-products--form'),
            optionManager;

        $.subscribe('plugin/swAjaxVariant/onBeforeRequestData', function() {
            $form = $('.custom-products--form');
            optionManager = $form.data('plugin_optionManager');

            if ($form.length && optionManager.hasOwnProperty('_hash')) {
                hash = optionManager._hash;
            }

            StateManager.destroyPlugin('*[data-custom-products-autosize-textarea="true"]', 'swagCustomProductsAutoSizeTextArea');
            StateManager.destroyPlugin('*[data-swag-custom-products-option-manager="true"]', 'swagCustomProductsOptionManager');
            StateManager.destroyPlugin('*[data-custom-products-collapse-panel="true"]', 'swagCustomProductsCollapsePanel');
            StateManager.destroyPlugin('*[data-datepicker="true"]', 'swDatePicker');
            StateManager.destroyPlugin('*[data-custom-products-number-field="true"]', 'swagCustomProductsNumberfield');
            StateManager.destroyPlugin('*[data-swag-custom-products-wysiwyg="true"]', 'swagCustomProductsWysiwyg');
            StateManager.destroyPlugin('*[data-description-plugin="true"]', 'swagCustomProductsDescription');
            StateManager.destroyPlugin('.custom-products--open-wizard', 'swagCustomProductsWizard');
        });

        $.subscribe('plugin/swAjaxVariant/onRequestData', function() {
            $form = $('.custom-products--form');

            if (!$form.length) {
                return;
            }

            if (hash) {
                window.history.replaceState(undefined, undefined, '#' + hash);
                OptionManager.prototype._hash = hash;
            }

            StateManager.addPlugin('*[data-custom-products-autosize-textarea="true"]', 'swagCustomProductsAutoSizeTextArea')
                .addPlugin('*[data-swag-custom-products-option-manager="true"]', 'swagCustomProductsOptionManager')
                .addPlugin('*[data-custom-products-collapse-panel="true"]', 'swagCustomProductsCollapsePanel')
                .addPlugin('*[data-datepicker="true"]', 'swDatePicker')
                .addPlugin('*[data-custom-products-number-field="true"]', 'swagCustomProductsNumberfield')
                .addPlugin('*[data-swag-custom-products-wysiwyg="true"]', 'swagCustomProductsWysiwyg')
                .addPlugin('*[data-description-plugin="true"]', 'swagCustomProductsDescription')
                .addPlugin(
                    '.custom-products--open-wizard',
                    'swagCustomProductsWizard',
                    [ 'm', 'l', 'xl' ]
                ).addPlugin(
                '.custom-products--open-wizard',
                'swagCustomProductsWizard',
                { modalWidth: '100%', height: '100%' },
                [ 'xs', 's' ]
            );

            $('*[data-swag-custom-products-upload="true"]').swagCustomProductsUpload();
        });
    });
})(jQuery, Handlebars, Modernizr, window);
