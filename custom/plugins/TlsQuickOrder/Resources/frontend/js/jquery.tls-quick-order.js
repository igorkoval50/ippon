;(function ($) {
    'use strict';

    $.plugin('tlsQuickOrder', {
        defaults: {
            errorSelector: '.error--message',
            orderNumberErrorSelector: 'span',
            hiddenClass: 'is--hidden',
            quantitySelector: '.quantity--select',
            totalSelector: '.total span'
        },

        init: function () {
            var me = this;

            me.applyDataAttributes();

            me.$error = me.$el.find(me.opts.errorSelector);
            me.$orderNumberError = me.$el.find(me.opts.orderNumberErrorSelector);
            me.$el.on('submit', $.proxy(me.onSubmit, me));

            me.$total = me.$el.find(me.opts.totalSelector);
            me._on(me.$el.find(me.opts.quantitySelector), 'change', $.proxy(me.onChangeQuantity, me));
        },

        onSubmit: function (event) {
            var me = this;

            event.preventDefault();

            $.loadingIndicator.open({
                'openOverlay': true
            });

            $.ajax({
                url: me.$el.attr('action'),
                data: me.$el.serialize(),
                method: 'POST',
                success: function (response) {
                    if (!response.success) {
                        if (response.orderNumber) {
                            me.showError(response.orderNumber);
                        }
                        return;
                    }

                    // Open Ajax Cart
                    $('*[data-collapse-cart="true"]')
                        .data('plugin_swCollapseCart')
                        .onMouseEnter({
                            preventDefault: function () {
                            }
                        });
                },
                complete: function () {
                    $.loadingIndicator.close();
                }
            });
        },
        showError: function (ordernumber) {
            var me = this;

            if (me.$error.length) {
                if (me.$orderNumberError.length) {
                    me.$orderNumberError.html(ordernumber);
                }
                me.$error.removeClass(me.opts.hiddenClass);
                setTimeout(function () {
                    me.$error.addClass(me.opts.hiddenClass);
                }, 3000);
            }
        },
        onChangeQuantity: function () {
            var me = this,
                sum = 0;

            me.$el.find(me.opts.quantitySelector).each(function () {
                if (this.options[this.selectedIndex].value && this.dataset.price) {
                    sum += parseInt(this.options[this.selectedIndex].value) *
                        parseFloat(this.dataset.price.replace(',', '.'));
                }
            });

            // me.$total.html(Number(sum).toLocaleString('de-DE', {minimumFractionDigits: 2, useGrouping: false}));
            me.$total.html(sum.toFixed(2).replace('.', ','));
        }
    });

    window.StateManager.addPlugin('*[data-TlsQuickOrder="true"]', 'tlsQuickOrder');
    window.StateManager.addPlugin('.tlsquickorder-collapse--title', 'swCollapsePanel', {
        contentSiblingSelector: '.tlsquickorder-collapse--content'
    });
})(jQuery);
