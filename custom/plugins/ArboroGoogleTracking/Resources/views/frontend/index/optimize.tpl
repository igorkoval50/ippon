{block name='arboro_tracking_optimize'}
    {block name='arboro_tracking_optimize_anti_flicker'}
        {if $optimizeAntiFlickerSnippet}
            <!-- Anti-flicker snippet (recommended)  -->
            <style>.async-hide { opacity: 0 !important} </style>
        {literal}
            <noscript data-tag="arboro-google-tracking">
                (function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
                    h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
                    (a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
                })(window,document.documentElement,'async-hide','{/literal}{$optimizeDataLayerName|escape}',{$optimizeTimeout}{literal},
                    {'{/literal}{$optimizeCID}{literal}':true});
            </noscript>{/literal}
        {/if}
    {/block}

    {if $trackingType=="GTM"}
        {block name='arboro_tracking_optimize_gtm'}
            <noscript data-tag="arboro-google-tracking">
                {literal}(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');{/literal}
                {include file='frontend/index/optimize_options.tpl'}
            </noscript>
        {/block}
    {/if}
{/block}
