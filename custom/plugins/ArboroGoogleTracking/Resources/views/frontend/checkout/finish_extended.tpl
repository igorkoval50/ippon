{extends file="parent:frontend/index/index.tpl"}

{block name='frontend_arboro_tracking'}
    {if $optimizeCID && 'checkout_finish'|in_array:$optimizeDisplayConfig}
        {include file='frontend/index/optimize.tpl'}
    {/if}

    {$smarty.block.parent}

    {if $trackingType == "UA"}
        {block name='frontend_arboro_tracking_checkout_finish'}
            <noscript data-tag="arboro-google-tracking">
                    ga('ec:setAction', 'purchase', {
                        'id': '{$sOrderNumber|escape}',
                        'revenue': '{$sAmount|escape}',
                        'tax': '{$sAmountTax|escape}',
                        'shipping': '{$sShippingcosts|escape}',
                        'affiliation': '{$sShopname|escape}',
                        'city': '{$sUserData.billingaddress.city|escape}',
                        'country': '{$sUserData.additional.country.countryen|escape}'
                    });
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
                    {literal}ga('send', 'event', 'checkout', 'finished');{/literal}
            </noscript>
        {/block}
    {/if}

    {if $conversionID}
        <!-- Event snippet for conversion page -->
        <noscript data-tag="arboro-google-tracking">{literal}
            gtag('event', 'conversion', {
                'send_to': {/literal}'AW-{$conversionID}/{$conversionLabel}'{literal},
                'value': {/literal}{$conversionAmount}{literal},
                'currency': {/literal}'{$conversionCurrency}'{literal},
                'transaction_id': {/literal}'{$sOrderNumber|escape}'{literal}
            });{/literal}
        </noscript>
    {/if}
{/block}

{block name='frontend_arboro_data_layer'}
{$smarty.block.parent}

    {if $trackingType == "GTM"}
        {block name='frontend_arboro_tracking_checkout_finish'}
            <noscript data-tag="arboro-google-tracking">
                    {$dataLayerName|escape}{literal}.push({
                        'ecommerce': {
                            'purchase': {
                                'actionField': {
                                    'id': '{/literal}{$sOrderNumber|escape}{literal}',
                                    'revenue': '{/literal}{$sAmount|escape}{literal}',
                                    'tax': '{/literal}{$sAmountTax|escape}{literal}',
                                    'shipping': '{/literal}{$sShippingcosts|escape}{literal}'
                                },
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
                        }
                    });
                    {/literal}
            </noscript>
        {/block}
    {/if}
{/block}

{block name='frontend_arboro_tracking'}
{$smarty.block.parent}

    {if $conversionID}
        {if $enableRemarketing}
            {block name='frontend_arboro_tracking_checkout_finish_remarketing'}
                <noscript data-tag="arboro-google-tracking" type="text/javascript">
                    {literal}
                    var google_tag_params = {
                        ecomm_prodid: [
                            {/literal}{foreach $sBasket.content as $sArticle}
                            "{$sArticle.ordernumber|escape}",
                            {/foreach}{literal}
                        ],
                        ecomm_pagetype: "purchase",
                        ecomm_totalvalue: {/literal}{$sAmount|replace:",":"."|escape}{literal}
                    };
                    {/literal}
                </noscript>

                {block name='frontend_arboro_tracking_checkout_finish_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
