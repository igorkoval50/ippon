;(function ($) {
    'use strict';

    $.subscribe('plugin/swAjaxVariant/onChange', function (event, plugin, values, $target) {
        if ($target.data('tlsResetOther')) {
            for (var prop in values) {
                if (prop !== $target.attr("name")) {
                    delete values[prop];
                }
            }
        }
    });
})(jQuery);
