{if {$userId}}
    {literal}
        ga('create', '{/literal}{$trackingID|escape}{literal}', 'auto', {
            userId: {/literal}{$userId|escape}{literal}
        });
    {/literal}
{else}
    {literal}ga('create', '{/literal}{$trackingID|escape}{literal}', 'auto');{/literal}
{/if}

{if $optimizeCID}
    {literal}ga('require', '{/literal}{$optimizeCID|escape}{literal}');{/literal}
{/if}

{if $forceSSL}
    ga('set', 'forceSSL', true);
{/if}

{if $anonymizeIp}
    ga('set', 'anonymizeIp', true);
{/if}

{if $displayFeatures}
    ga('require', 'displayfeatures');
{/if}

{if $cleanURL}
    ga('require', 'cleanUrlTracker');
{/if}

{if $outboundForm}
    ga('require', 'outboundFormTracker');
{/if}

{if $outboundLink}
    ga('require', 'outboundLinkTracker');
{/if}

{if $pageVisibility}
    ga('require', 'pageVisibilityTracker');
{/if}

{if $socialWidget}
    ga('require', 'socialWidgetTracker');
{/if}

{if $urlChange}
    ga('require', 'urlChangeTracker');
{/if}

{if $enhancedEcommerce}
    ga('require', 'ec');
{/if}

{if $adWordsTracking}
    {literal}
        var match = RegExp('[?&]gclid=([^&]*)').exec(window.location.search);
        var gclid = match && decodeURIComponent(match[1].replace(/\+/g, ' '));
        if(gclid){
            ga('set', '{/literal}{$adWordsDimension|escape}{literal}', gclid);
        }
    {/literal}
{/if}


ga('send', 'pageview');

{if {$trackBounce}}
    {literal}ga('set', 'nonInteraction', true);
    setTimeout("ga('send', 'event', 'read', '{/literal}{$bounceTime|escape}{literal} seconds')", {/literal}{$bounceTime|escape * 1000}{literal});{/literal}
{/if}
