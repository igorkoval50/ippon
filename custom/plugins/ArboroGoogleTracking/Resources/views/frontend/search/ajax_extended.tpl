{extends file="parent:frontend/search/ajax.tpl"}

{block name="search_ajax_inner"}
    {$smarty.block.parent}

    {block name='arboro_tracking_search_ajax'}
        <noscript data-tag="arboro-google-tracking">
        {if $trackingType == "UA"}
            {foreach name=articles from=$sSearchResults.sResults item=sArticle}
                {literal}
                    ga('ec:addImpression', {
                        'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                        'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                        'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                        {/literal}{if $brandTracking }{literal}
                        'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                        {/literal}{/if}{literal}
                        'list': 'Quick Search Results',
                        'position': {/literal}{$smarty.foreach.articles.iteration}{literal}
                    });
                {/literal}
            {/foreach}
            {literal}ga('send', 'event', 'search', 'view');{/literal}
        {elseif $trackingType == "GTM"}
            {$dataLayerName|escape}{literal}.push({
                'ecommerce': {
                    'impressions': [{/literal}
                        {foreach name=articles from=$sSearchResults.sResults item=sArticle}{literal}
                        {
                            'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                            'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                            'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                            {/literal}{if $brandTracking }{literal}
                            'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                            {/literal}{/if}{literal}
                            'list': 'Quick Search Results',
                            'position': {/literal}{$smarty.foreach.articles.iteration}{literal}
                        },
                        {/literal}{/foreach}
                        {literal}]
                }
            });
            {/literal}
        {/if}
        </noscript>
    {/block}
{/block}

{*
{block name="frontend_arboro_tracking"}
    {$smarty.block.parent}
    
    {if $conversionID}
        {if $enableRemarketing}
            {block name='arboro_tracking_search_ajax_remarketing'}
                <script type="text/javascript">
                    {literal}
                    var google_tag_params = {
                        ecomm_prodid: [
                            {/literal}{foreach name=articles from=$sSearchResults.sResults item=sArticle}
                            "{$sArticle.ordernumber|escape}",
                            {/foreach}{literal}
                        ],
                        ecomm_pagetype: "searchresults"
                    };
                    {/literal}
                </script>

                {block name='arboro_tracking_search_ajax_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
*}
