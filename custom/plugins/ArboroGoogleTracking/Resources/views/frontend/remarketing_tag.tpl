<noscript data-tag="arboro-google-tracking">
    {literal}
    gtag('event', 'page_view',
        {
            'send_to': 'AW-{/literal}{$conversionID|escape}{literal}',
            'ecomm_pagetype': '{/literal}{$ecomm_pagetype}{literal}',
            'ecomm_prodid': {/literal}{$ecomm_prodid}{literal}
        }
    );
    {/literal}
</noscript>