{block name="frontend_index_header_javascript_tracking"}
    {$smarty.block.parent}

    {block name='arboro_tracking_cookie_banner'}
    {literal}
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
        <script>
            window.addEventListener("load", function(){
                window.cookieconsent.initialise({
                    "palette": {
                        {/literal}
                        {if $cookieBgColor}
                        "popup": {
                            "background": "{$cookieBgColor}"
                        },
                        {/if}
                        {if $cookieBgColor}
                        "button": {
                            "background": "{$cookieBtnColor}"
                        }
                        {/if}
                        {literal}
                    },
                    "revokeBtn": '<div></div>',
                    {/literal}
                    "position": "{$cookieBannerPosition}",
                    {if !$cookieBannerMore}
                    'showLink': false,
                    {/if}
                    {literal}
                    "type": "opt-out",
                    "content": {
                        "message": '{/literal}{s name="cookieMessageNew" namespace="frontend/ArboroGoogleTracking"}Diese Website benutzt Cookies, die für den technischen Betrieb der Website erforderlich sind und stets gesetzt werden.<br>{/s}'+
                            '{s name="cookieMessageServices" namespace="frontend/ArboroGoogleTracking"}Durch einen Klick auf <strong>{$cookieAcceptAll}</strong> werden zusätzlich Cookies für <strong>Google Analytics</strong> akzeptiert.<br>Diese Cookie werden genutzt, um den Traffic auf dieser Website zu analysieren. Informationen zu Ihrer Nutzung unserer Website werden daher an Google übermittelt.<br>{/s}'+
                            '{if $cookieBtnInText}{s name="cookieBtnInText" namespace="frontend/ArboroGoogleTracking"}Durch einen Klick auf <a aria-label="deny cookies" role="button" tabindex="0" class="cc-btn cc-deny cookie--btn-in-text">{$cookieAcceptTechnical}</a> können Sie diese Cookies ablehnen.<br>{/s}{/if}'+
                            '{if $cookieMenu}{s name="cookieMenuInfo" namespace="frontend/ArboroGoogleTracking"}Die Cookie Einstellungen können Sie jederzeit nachträglich noch im Footer ändern.<br>{/s}{/if}'+
                            '{if $cookieBannerMore}{s name="cookieMoreInfo" namespace="frontend/ArboroGoogleTracking"}Mehr Informationen: {/s}{/if}',{literal}
                        "dismiss": '{/literal}{s name="cookieDismissAll" namespace="frontend/ArboroGoogleTracking"}Alle akzeptieren{/s}{literal}',
                        "deny": '{/literal}{s name="cookieDeny" namespace="frontend/ArboroGoogleTracking"}Nur technisch notwendige akzeptieren{/s}{literal}',
                        "link": '{/literal}{$cookieBannerLinkText}{literal}',
                        "href": '{/literal}{$cookieBannerLink|escape}{literal}',
                    },
                    onStatusChange: function(status, chosenBefore) {
                        if (this.hasConsented()) {
                            gaOptin();
                            activateTracking();
                        } else {
                            deleteGoogleCookies();
                        }
                    }
                })
                {/literal}{if $cookieBtnInText}
                $('.cc-compliance .cc-btn.cc-deny').hide();
                {/if}{literal}
            });
            {/literal}
        </script>
    {/block}
{/block}

{block name='frontend_index_header_javascript_jquery_lib'}
    {$smarty.block.parent}

    {if $cookieMenu}
        <script type="text/javascript">
            {literal}
            document.asyncReady(function(){
                $({/literal}'#{$cookieMenuId}'{literal}).click(function(e){
                    e.preventDefault();
                    $.loadingIndicator.open({
                        'openOverlay': true
                    });
                    $.ajax({
                        dataType: 'html',
                        method: 'POST',
                        url: {/literal}'{url controller=ArboroGoogleTracking action=cookieSettingsMenu}'{literal},
                        success: function (result) {
                            $.loadingIndicator.close(function () {
                                $.modal.open(result, {
                                    width: 750,
                                    sizing: 'content'
                                });

                                var disableStr = 'ga-disable-' + document.getElementById('arboroTracking').getAttribute("data-id");
                                if (document.cookie.indexOf(disableStr + '=true') === -1) {
                                    document.getElementById("arboro-switch-ga").checked = true;
                                }

                                $('.arboro-cookie-settings-menu--btn').click(function() {
                                    $('.arboro-cookie-settings-menu--label').delay("fast").fadeIn();
                                    var gaCheck = document.getElementById("arboro-switch-ga").checked;

                                    if(gaCheck) {
                                        gaOptin();
                                    } else {
                                        deleteGoogleCookies();
                                        gaOptout();
                                    }
                                    $('.arboro-cookie-settings-menu--label').delay(3000).fadeOut();
                                });
                            });
                        }
                    });
                });
            });
            {/literal}
        </script>
    {/if}
{/block}
