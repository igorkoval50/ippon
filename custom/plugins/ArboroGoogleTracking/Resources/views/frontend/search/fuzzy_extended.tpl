{extends file="parent:frontend/search/fuzzy.tpl"}

{block name='frontend_arboro_tracking'}
{$smarty.block.parent}

    {if $trackingType == "UA"}
        {block name='arboro_tracking_search_fuzzy'}
            {counter start=0 skip=1 print=false}
            <noscript data-tag="arboro-google-tracking">
                    {literal}ga('set', '&cu', '{/literal}{$shopCurrency|escape}{literal}');{/literal}
                    {foreach $sSearchResults.sArticles as $sArticle}
                        {literal}
                            ga('ec:addImpression', {
                                'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                                'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                                'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                                {/literal}{if $brandTracking }{literal}
                                'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                                {/literal}{/if}{literal}
                                'list': 'Search Results',
                                'position': {/literal}{counter}{literal}
                            });
                        {/literal}
                    {/foreach}
                    {literal}ga('send', 'event', 'search', 'view');{/literal}
            </noscript>
        {/block}
    {/if}
{/block}

{block name='frontend_arboro_data_layer'}
{$smarty.block.parent}

    {if $trackingType == "GTM"}
        {block name='arboro_tracking_search_fuzzy'}
            {counter start=0 skip=1 print=false}
            <noscript data-tag="arboro-google-tracking">
                    {$dataLayerName|escape}{literal}.push({
                        'ecommerce': {
                            'currencyCode': '{/literal}{$shopCurrency|escape}{literal}',
                            'impressions': [{/literal}
                                {foreach $sSearchResults.sArticles as $sArticle}{literal}
                                {
                                    'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                                    'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                                    'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                                    {/literal}{if $brandTracking }{literal}
                                    'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                                    {/literal}{/if}{literal}
                                    'list': 'Search Results',
                                    'position': {/literal}{counter}{literal}
                                },
                                {/literal}{/foreach}
                                {literal}]
                        }
                    });
                    {/literal}
            </noscript>
        {/block}
    {/if}
{/block}

{*
{block name="frontend_arboro_tracking"}
    {$smarty.block.parent}
    
    {if $conversionID}
        {if $enableRemarketing}
            {block name='arboro_tracking_search_fuzzy_remarketing'}
                <script type="text/javascript">
                    {literal}
                    var google_tag_params = {
                        ecomm_prodid: [
                            {/literal}{foreach $sSearchResults.sArticles as $sArticle}
                            "{$sArticle.ordernumber|escape}",
                            {/foreach}{literal}
                        ],
                        ecomm_pagetype: "searchresults"
                    };
                    {/literal}
                </script>

                {block name='arboro_tracking_search_fuzzy_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
*}
