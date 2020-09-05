{extends file="parent:frontend/index/index.tpl"}

{block name='frontend_index_header_meta_http_tags'}
    {block name='frontend_arboro_tracking'}
        {if $trackingType=="GTM"}
            {block name='frontend_arboro_data_layer'}
                <noscript data-tag="arboro-google-tracking">
                    {if $userId}
                    window.{$dataLayerName|escape} = window.{$dataLayerName|escape} || [{literal}{'{/literal}{$userIdName|escape}{literal}': '{/literal}{$userId|escape}{literal}'}{/literal}];
                    {else}
                    window.{$dataLayerName|escape} = window.{$dataLayerName|escape} || [];
                    {/if}
                </noscript>
            {/block}
            {block name='arboro_tracking_gtm'}
                {include file='frontend/index/tag_manager.tpl'}
            {/block}
        {elseif $trackingType=="UA"}
            {block name='arboro_tracking_analytics'}
                {include file='frontend/index/analytics.tpl'}
            {/block}
        {/if}

        {block name='frontend_arboro_conversion_tag'}
            {if $conversionID}
                <!-- Global site tag (gtag.js) - Google Ads -->
                <noscript data-tag="arboro-google-tracking" async src="//www.googletagmanager.com/gtag/js?id=AW-{$conversionID|escape}"></noscript>
                <noscript data-tag="arboro-google-tracking">{literal}
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config',{/literal}'AW-{$conversionID|escape}'{literal});{/literal}
                </noscript>
            {/if}
        {/block}
    {/block}

    {$smarty.block.parent}
{/block}

{block name="frontend_index_header_javascript"}
    {block name='frontend_arboro_tracking_object'}
        {if $useCollectedJS == false}
            <noscript data-tag="arboro-google-tracking" type="text/javascript" src="{link file='frontend/_resources/javascript/ArboroGoogleTracking.min.js'}"></noscript>
        {/if}
        <div id="arboroTracking" style="display: none;" data-id="{$trackingID|escape}" {if $dataLayerName}data-name="{$dataLayerName|escape}"{/if} data-type="{$trackingType|escape}" data-brand="{$brandTracking}" data-cookie-banner="{if $cookieAcceptAll}true{else}false{/if}" data-cctype="{$enableCookieConsent}"></div>
    {/block}

    {$smarty.block.parent}
{/block}

{block name="frontend_index_after_body"}
    {if $trackingType=="GTM"}
        {block name='arboro_tracking_gtm_noscript'}
        {literal} <noscript><iframe src="//www.googletagmanager.com/ns.html?id={/literal}{$trackingID|escape}{literal}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>{/literal}
        {/block}
    {/if}

    {$smarty.block.parent}
{/block}

{block name='frontend_index_footer_vatinfo'}
    {if $cookieMenu && $cookieMenuId === 'arboro-cookie-menu'}
        <a href="#" id="arboro-cookie-menu" class="btn">{s name="cookieMenuLink" namespace="frontend/ArboroGoogleTracking"}Cookie Einstellungen{/s}</a>
    {/if}

    {$smarty.block.parent}
{/block}
