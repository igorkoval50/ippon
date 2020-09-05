$(document).ready(function() {
    $('body').kssSliderArticlesBuy();
});
;(function($) {
    "use strict";
    $.plugin('kssSliderArticlesBuy', {

        defaults: {},

        init: function() {
            this.registerEvents($('body'));
        },
        registerEvents: function(context) {
            var me = this;

            $('.kss-configurator select', context).on('change', function() {
                me.realoadVariantData(this);
            });
        },
        realoadVariantData: function (item) {
            var me = this;

            var form = $(item).closest('form');
            var listingBuyContainer = $(item).closest('.kss-configurator');
            var productBox = $(item).closest('.product--box');
            var url = $(listingBuyContainer).data('ordernumber-search-url');
            $.post(url, $(form).serialize(), function( data ) {
                var parsed = $.parseHTML(data);
                var orderNumber = $(parsed).find("input[name='sAdd']").val();
                var configuratorHtml = $(parsed).find(".kss-configurator").html();
                var priceHtml = $(parsed).find(".product--price-info").html();
                $(".product--price-info", productBox).html(priceHtml);
                $(".kss-configurator", productBox).html(configuratorHtml);
                $("input[name='sAdd']", productBox).val(orderNumber);
                me.registerEvents(productBox);
            })

        },

        destroy: function() {

        }
    });
})(jQuery);