{extends file="parent:frontend/listing/index.tpl"}

{block name='frontend_arboro_tracking'}
    {if $optimizeCID && 'listing'|in_array:$optimizeDisplayConfig}
        {include file='frontend/index/optimize.tpl'}
    {/if}
    
    {$smarty.block.parent}

    {if $trackingType == "UA"}
        {block name='frontend_arboro_tracking_listing'}
            <noscript data-tag="arboro-google-tracking">
                {counter start=0 skip=1 print=false}
                    {literal}ga('set', '&cu', '{/literal}{$shopCurrency|escape}{literal}');{/literal}
                    {foreach $sArticles as $sArticle}{literal}
                        ga('ec:addImpression', {
                            'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                            'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                            'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                            {/literal}{if $brandTracking }{literal}
                            'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                            {/literal}{/if}{literal}
                            'category': '{/literal}{$sCategoryInfo.name|escape}{literal}',
                            'list': 'Category',
                            'position': {/literal}{counter}{literal}
                        });
                        {/literal}
                    {/foreach}
                    {literal}ga('send', 'event', 'listing', 'view');{/literal}
            </noscript>
        {/block}
    {/if}
{/block}

{block name='frontend_arboro_data_layer'}
{$smarty.block.parent}

    {if $trackingType == "GTM"}
        {block name='frontend_arboro_tracking_listing'}
            <noscript data-tag="arboro-google-tracking">
                {counter start=0 skip=1 print=false}

                    {$dataLayerName|escape}{literal}.push({
                        'ecommerce': {
                            'currencyCode': '{/literal}{$shopCurrency|escape}{literal}',
                            'impressions': [{/literal}
                                {foreach $sArticles as $sArticle}{literal}
                                {
                                    'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                                    'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                                    'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                                    {/literal}{if $brandTracking }{literal}
                                    'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                                    {/literal}{/if}{literal}
                                    'category': '{/literal}{$sCategoryInfo.name|escape}{literal}',
                                    'list': 'Category',
                                    'position': {/literal}{counter}{literal}
                                },
                                {/literal}{/foreach}{literal}
                            ]
                        }
                    });
                    {/literal}
            </noscript>
        {/block}
    {/if}
{/block}

{block name="frontend_arboro_tracking"}
    {$smarty.block.parent}

    {if $conversionID}
        {if $enableRemarketing}
            {block name='arboro_tracking_listing_remarketing'}
                <noscript data-tag="arboro-google-tracking" type="text/javascript">
                    {literal}
                    var google_tag_params = {
                        ecomm_prodid: [
                            {/literal}{foreach $sArticles as $sArticle}
                            "{$sArticle.ordernumber|escape}",
                            {/foreach}{literal}
                        ],
                        ecomm_pagetype: "category"
                    };
                    {/literal}
                </noscript>

                {block name='arboro_tracking_listing_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
