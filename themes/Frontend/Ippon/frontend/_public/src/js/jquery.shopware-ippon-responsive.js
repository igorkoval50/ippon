(function($, window) {

    window.StateManager


        .addPlugin('*[data-add-kssarticle="true"]', 'kssAddArticle')

    ;

    $(function($) {
        // Ajax cart amount display
        function cartRefresh() {
            var ajaxCartRefresh = window.controller.ajax_cart_refresh,
                $cartAmount = $('.cart--amount'),
                $cartQuantity = $('.cart--quantity');

            if (!ajaxCartRefresh.length) {
                return;
            }

            $.publish('plugin/swResponsive/onCartRefresh');

            $.ajax({
                url: ajaxCartRefresh,
                dataType: 'json',
                success: function (cart) {
                    if (!cart.amount || isNaN(cart.quantity)) {
                        return;
                    }

                    $cartAmount.html(cart.amount);
                    $cartQuantity.html(cart.quantity).removeClass('is--hidden');

                    if (cart.quantity === 0) {
                        $cartQuantity.addClass('is--hidden');
                    }

                    $.publish('plugin/swResponsive/onCartRefreshSuccess', [ cart ]);
                }
            });
        }
        $.subscribe('plugin/kssAddArticle/onAddArticle', cartRefresh);
    });
})(jQuery, window);
