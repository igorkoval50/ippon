{extends file="parent:frontend/checkout/cart.tpl"}

{block name='frontend_arboro_tracking'}
    {if $optimizeCID && 'checkout_cart'|in_array:$optimizeDisplayConfig}
        {include file='frontend/index/optimize.tpl'}
    {/if}
    
    {$smarty.block.parent}

    {if $trackingType == "UA"}
        {block name='frontend_arboro_tracking_checkout_cart'}
            <noscript data-tag="arboro-google-tracking">

                    {literal}
                        ga('ec:setAction', 'checkout', {'step': {/literal}{$checkoutStep}{literal}});
                    {/literal}
                    {foreach $sBasket.content as $sArticle}{literal}
                        ga('ec:addProduct', {
                            'name': '{/literal}{$sArticle.articlename|escape}{literal}',
                            'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                            'price': '{/literal}{$sArticle.priceNumeric|escape}{literal}',
                            {/literal}{if $brandTracking }{literal}
                            'brand': '{/literal}{$sArticle.additional_details.supplierName|escape}{literal}',
                            {/literal}{/if}{literal}
                            'variant': '{/literal}{$sArticle.additional_details.additionaltext|escape}{literal}',
                            'quantity': {/literal}{$sArticle.quantity|escape|intval}{literal}
                        });
                        {/literal}
                    {/foreach}
                    {literal}ga('send', 'event', 'checkout', 'step' + {/literal}{$checkoutStep}{literal});{/literal}
            </noscript>
        {/block}
    {/if}
{/block}

{block name='frontend_arboro_data_layer'}
    {$smarty.block.parent}

    {if $trackingType == "GTM"}
        {block name='frontend_arboro_tracking_checkout_cart'}
            <noscript data-tag="arboro-google-tracking">
                    {$dataLayerName|escape}{literal}.push({
                        'event': 'checkout',
                        'ecommerce': {
                            'checkout': {
                                'actionField': {'step': {/literal}{$checkoutStep}{literal}},
                                'products': [{/literal}
                                    {foreach $sBasket.content as $sArticle}{literal}
                                    {
                                        'name': '{/literal}{$sArticle.articlename|escape}{literal}',
                                        'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                                        'price': '{/literal}{$sArticle.priceNumeric|escape}{literal}',
                                        {/literal}{if $brandTracking }{literal}
                                        'brand': '{/literal}{$sArticle.additional_details.supplierName|escape}{literal}',
                                        {/literal}{/if}{literal}
                                        'quantity': {/literal}{$sArticle.quantity|escape|intval}{literal}
                                    },
                                    {/literal}{/foreach}
                                    {literal}]
                            }
                        },
                        'eventCallback': function() {
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
            {block name='frontend_arboro_tracking_checkout_cart_remarketing'}
                <noscript data-tag="arboro-google-tracking" type="text/javascript">
                    {literal}
                    var google_tag_params = {
                        ecomm_prodid: [
                            {/literal}{foreach $sBasket.content as $sBasketItem}
                            "{$sBasketItem.ordernumber|escape}",
                            {/foreach}{literal}
                        ],
                        ecomm_pagetype: "cart",
                        ecomm_totalvalue: {/literal}{$sBasket.Amount|replace:",":"."|escape}{literal}
                    };
                    {/literal}
                </noscript>

                {block name='frontend_arboro_tracking_checkout_cart_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
