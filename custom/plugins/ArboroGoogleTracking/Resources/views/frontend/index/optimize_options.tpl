{if {$userId}}
    {literal}
        ga('create', '{/literal}{$optimizeUAID|escape}{literal}', 'auto', {
            userId: {/literal}{$userId|escape}{literal}
        });
    {/literal}
{else}
    {literal}ga('create', '{/literal}{$optimizeUAID|escape}{literal}', 'auto');{/literal}
{/if}


{literal}ga('require', '{/literal}{$optimizeCID|escape}{literal}');{/literal}

{if $optimizeAnonymizeIp}
    ga('set', 'anonymizeIp', true);
{/if}

{if $optimizeDisplayFeatures}
    ga('require', 'displayfeatures');
{/if}

{if $optimizeEnhancedEcommerce}
    ga('require', 'ec');
{/if}
