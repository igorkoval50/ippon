<!-- Google Tag Manager -->
{if $loadAsync}
<noscript data-tag="arboro-google-tracking">{literal}(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','{/literal}{$dataLayerName|escape}{literal}','{/literal}{$trackingID|escape}{literal}'{/literal});
</noscript>
{else}
<noscript data-tag="arboro-google-tracking">{literal}(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=false;j.src=
            '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','{/literal}{$dataLayerName|escape}{literal}','{/literal}{$trackingID|escape}{literal}'{/literal});
</noscript>
{/if}
<!-- End Google Tag Manager -->
