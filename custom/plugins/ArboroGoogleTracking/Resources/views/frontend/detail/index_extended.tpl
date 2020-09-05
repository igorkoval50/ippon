{extends file="parent:frontend/detail/index.tpl"}

{block name='frontend_arboro_tracking'}
    {if $optimizeCID && 'detail'|in_array:$optimizeDisplayConfig}
        {include file='frontend/index/optimize.tpl'}
    {/if}

    {$smarty.block.parent}

    {if $trackingType == "UA"}
        {block name='frontend_arboro_tracking_detail'}
            {function variantString}{foreach $data as $group}{foreach $group.values as $option}{if $option.selected}{capture name="test"}[{$group.groupname|escape}] {$option.optionname|escape} {/capture}{strip}{$smarty.capture.test|strip|regex_replace:"/[\r\n]/" : " "}{/strip}{/if}{/foreach}{/foreach}{/function}
            <noscript data-tag="arboro-google-tracking">
                    {literal}
                    ga('ec:addProduct', {
                        'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                        'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                        'category': '{/literal}{$sCategoryInfo.name|escape}{literal}',
                        {/literal}{if $brandTracking }{literal}
                        'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                        {/literal}{/if}{literal}
                        'variant': '{/literal}{call variantString data=$sArticle.sConfigurator}{literal}',
                        'price': '{/literal}{$sArticle.price_numeric|escape}{literal}'
                    });
                    {/literal}
                    {literal}ga('ec:setAction', 'detail', {'list': '{/literal}{$sCategoryInfo.name|escape}{literal}'});{/literal}
                        {foreach key=similarPosition item=sSimilarArticle from=$sArticle.sSimilarArticles}
                            {if $sSimilarArticle.articleName != ''}
                            {literal}
                            ga('ec:addImpression', {
                                'name': '{/literal}{$sSimilarArticle.articleName|escape}{literal}',
                                'id': '{/literal}{$sSimilarArticle.ordernumber|escape}{literal}',
                                'price': '{/literal}{$sSimilarArticle.price_numeric|escape}{literal}',
                                {/literal}{if $brandTracking }{literal}
                                'brand': '{/literal}{$sSimilarArticle.supplierName|escape}{literal}',
                                {/literal}{/if}{literal}
                                'list': 'Similar Articles',
                                'position': {/literal}{$similarPosition}{literal}
                            });
                            {/literal}
                            {/if}
                        {/foreach}
                        {literal}ga('send', 'event', 'detail', 'view');{/literal}
            </noscript>
        {/block}
    {/if}
{/block}

{block name='frontend_arboro_data_layer'}
    {$smarty.block.parent}

    {if $trackingType == "GTM"}
        {block name='frontend_arboro_tracking_detail'}
            {function variantString}{foreach $data as $group}{foreach $group.values as $option}{if $option.selected}{capture name="test"}[{$group.groupname|escape}] {$option.optionname|escape} {/capture}{strip}{$smarty.capture.test|strip|regex_replace:"/[\r\n]/" : " "}{/strip}{/if}{/foreach}{/foreach}{/function}
            <noscript data-tag="arboro-google-tracking">
                    {$dataLayerName|escape}{literal}.push({
                        'ecommerce': {
                            'impressions': [{/literal}
                                {foreach key=similarPosition item=sSimilarArticle from=$sArticle.sSimilarArticles}
                                {if $sSimilarArticle.articleName != ''}
                                {literal}
                                {
                                    'name': '{/literal}{$sSimilarArticle.articleName|escape}{literal}',
                                    'id': '{/literal}{$sSimilarArticle.ordernumber|escape}{literal}',
                                    'price': '{/literal}{$sSimilarArticle.price_numeric|escape}{literal}',
                                    {/literal}{if $brandTracking }{literal}
                                    'brand': '{/literal}{$sSimilarArticle.supplierName|escape}{literal}',
                                    {/literal}{/if}{literal}
                                    'list': 'Similar Articles',
                                    'position': {/literal}{$similarPosition}{literal}
                                },
                                {/literal}
                                {/if}
                                {/foreach}
                                {literal}],
                            'detail': {
                                'actionField': {'list': '{/literal}{$sCategoryInfo.name|escape}{literal}'},
                                'products': [{
                                    'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                                    'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                                    'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                                    {/literal}{if $brandTracking }{literal}
                                    'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                                    {/literal}{/if}{literal}
                                    'category': '{/literal}{$sCategoryInfo.name|escape}{literal}',
                                    'variant': '{/literal}{call variantString data=$sArticle.sConfigurator}{literal}'
                                }]
                            }
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
            {block name='frontend_arboro_tracking_detail_remarketing'}
                <noscript data-tag="arboro-google-tracking" type="text/javascript">
                    var google_tag_params = {
                        ecomm_prodid: "{$sArticle.ordernumber|escape}",
                        ecomm_pagetype: "product",
                        ecomm_totalvalue: {$sArticle.price_numeric|escape}
                    };
                </noscript>

                {block name='frontend_arboro_tracking_detail_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
