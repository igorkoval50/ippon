{extends file="parent:frontend/index/index.tpl"}

{block name='frontend_arboro_tracking'}
    {if $optimizeCID}
        {if $sTargetAction == 'confirm' && $sAction == 'login' && 'checkout_reg'|in_array:$optimizeDisplayConfig}
            {include file='frontend/index/optimize.tpl'}
        {/if}
        {if $sTargetAction == 'confirm' && $sAction != 'login' && 'checkout_confirm'|in_array:$optimizeDisplayConfig}
            {include file='frontend/index/optimize.tpl'}
        {/if}
        {if $sTargetAction == 'shippingPayment' && 'checkout_payment_shipping'|in_array:$optimizeDisplayConfig}
            {include file='frontend/index/optimize.tpl'}
        {/if}
    {/if}

    {$smarty.block.parent}

    {if $trackingType == "UA"}
        {block name='frontend_arboro_tracking_checkout_confirm'}
            <noscript data-tag="arboro-google-tracking">
                    {literal}
                        ga('ec:setAction', 'checkout', {'step': {/literal}{$checkoutStep}{literal}, 'option': '{/literal}{$sPayment.description|escape}{literal}'});
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
        {block name='frontend_arboro_tracking_checkout_confirm'}
            <noscript data-tag="arboro-google-tracking">
                    {$dataLayerName|escape}{literal}.push({
                        'event': 'checkout',
                        'ecommerce': {
                            'checkout': {
                                'actionField': {'step': {/literal}{$checkoutStep}{literal}, 'option': '{/literal}{$sPayment.description|escape}{literal}'},
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
