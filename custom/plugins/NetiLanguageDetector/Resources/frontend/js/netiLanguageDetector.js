;'use strict';
var Neti              = Neti || {};
Neti.LanguageDetector = {
    'retryCount': 0,
    'url': '',
    'setUrl': function(url) {
        this.url = url;

        return this;
    },
    'getUrl': function() {
        return this.url;
    },
    'cookie': function(name, value, days) {
        var date,
            expires = '';

        if (days) {
            date = new Date();
            date.setTime(date.getTime() + (
                days * 24 * 60 * 60 * 1000
            ));
            expires = '; expires=' + date.toGMTString();
        }

        value = encodeURI(value);

        document.cookie = name + '=' + value + expires + '; path=/';
    },
    'dispatch': function() {
        var me     = this,
            plugin = $('*[data-cookie-permission="true"]').data('plugin_swCookiePermission');

        if (!plugin) {
            me.requestData();
            return;
        }

        plugin.displayCookiePermission(function(needUserInteraction) {
            // The user needs to accept, decline or configure the cookies
            if (needUserInteraction) {
                $.subscribe('plugin/swCookieConsentManager/onSave', function() {
                    me.requestData(); // Since we're using a technical cookie, we can continue
                });

                $('.cookie-permission--accept-button').one('click', function() {
                    me.requestData(); // The user accepts all cookies
                });

                $('.cookie-permission--decline-button').one('click', function() {
                    me.requestData(); // // Since we're using a technical cookie, we can continue
                });
            } else if (typeof window.hasCookiesAllowed === 'function') { // No user interaction required. Check if cookies are allowed.
                if (!window.hasCookiesAllowed()) {
                    return;
                }

                me.requestData();
            } else { // The cookie check method does not exist (in case the plugin does not exist)
                me.requestData();
            }
        });
    },

    'requestData': function() {
        var me        = this,
            $location = $(location);

        $.ajax({
            'url': this.getUrl(),
            'method': 'POST',
            'data': {
                'referrer': $location.attr('href'),
                'search': $location.attr('search'),
                'pathname': $location.attr('pathname')
            },
            'success': function(data) {
                me.processResponse(data);
            },
            'error': function() {
            },
            'complete': function() {
            }
        });
    },

    'processResponse': function(data) {
        var me = this;

        if (false === data.session && me.retryCount < 3) {
            ++me.retryCount;
            me.requestData();
            return;
        }

        if (false === data.status) {
            return;
        }

        if (false === data.modal) {
            if (data.redirect) {
                $(location).attr('href', data.redirect);
            }
        } else {
            $.modal.open(data.modal.content, {
                title: data.modal.title,
                sizing: 'content',
                width: 400,
                height: 'auto',
                additionalClass: 'js--modal--neti--redirect'
            });

            $('[data-redirect="true"]').bind('click', function() {
                $(this).parent('form').submit();
                return false;
            });

            $('[data-redirect="false"]').bind('click', function() {
                if (data.setCookie) {
                    me.cookie('disable-redirect', 1);
                }
                $.modal.close();
                return false;
            });
        }
    }
};
