{if $loadAsync}
    <noscript data-tag="arboro-google-tracking">
        {literal}window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;{/literal}
        {include file='frontend/index/analytics_options.tpl'}
    </noscript>
    <noscript data-tag="arboro-google-tracking" async src='//www.google-analytics.com/analytics.js'></noscript>
    {if $useCollectedJS == false}
        <noscript data-tag="arboro-google-tracking" async src="{link file='frontend/_resources/javascript/autotrack.js'}"></noscript>
    {/if}
{else}
    {if $useCollectedJS == false}
        <noscript data-tag="arboro-google-tracking" src="{link file='frontend/_resources/javascript/autotrack.js'}"></noscript>
    {/if}
    <noscript data-tag="arboro-google-tracking">
        {literal}(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');{/literal}
        {include file='frontend/index/analytics_options.tpl'}
    </noscript>
{/if}