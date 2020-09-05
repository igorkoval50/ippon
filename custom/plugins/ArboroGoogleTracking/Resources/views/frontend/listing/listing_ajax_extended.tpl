{extends file="parent:frontend/listing/index.tpl"}

{block name="frontend_listing_list_inline_ajax"}
    {$smarty.block.parent}

    {block name='arboro_tracking_listing_ajax'}
        {counter start=0 skip=1 print=false}
        <noscript data-tag="arboro-google-tracking">
            {if $trackingType == "UA"}
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
            {elseif $trackingType == "GTM"}
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
                            {/literal}{/foreach}
                            {literal}]
                    }
                });
                {/literal}
            {/if}
        </noscript>
    {/block}
{/block}

{block name="frontend_arboro_tracking"}
    {$smarty.block.parent}
    
    {if $conversionID}
        {if $enableRemarketing}
            {block name='arboro_tracking_listing_ajax_remarketing'}
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

                {block name='arboro_tracking_listing_ajax_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
