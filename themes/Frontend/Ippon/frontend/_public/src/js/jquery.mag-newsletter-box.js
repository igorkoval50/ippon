;(function ($) {
    var url = $('.newsletterbox--wrapper--inner').data('controller');
    if (url) {
        $.ajaxPrefilter(function (options) {
            if (options.url === unescape(url)) {
                var groups = $("#newsletterbox--form input[name='tls_newsletter_groups[]']").serialize();
                if (groups) {
                    options.data += '&' + groups;
                }
            } else if (options.url === unescape($('.newsletterbox--wrapper--inner').data('validatecontroller'))) {
                options.data += Math.floor(Math.random() * (9999999 - 1000000)) + 1000000;
            }
        });
    }
})(jQuery);
