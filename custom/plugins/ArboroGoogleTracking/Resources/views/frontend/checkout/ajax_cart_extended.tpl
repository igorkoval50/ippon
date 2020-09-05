{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart'}
    {$smarty.block.parent}
    
    {block name='frontend_arboro_tracking_checkout_ajax_cart'}
        <noscript data-tag="arboro-google-tracking">
            {if $trackingType == "UA"}
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
            {elseif $trackingType == "GTM"}
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
            {/if}
        </noscript>
    {/block}
{/block}
