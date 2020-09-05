{extends file="parent:frontend/account/ajax_login.tpl"}

{block name='frontend_account_ajax_login_action_buttons'}
{$smarty.block.parent}

    {block name='frontend_arboro_tracking_account_ajax_login'}
        <noscript data-tag="arboro-google-tracking">
            {if $trackingType == "UA"}
                {literal}
                    ga('ec:setAction', 'checkout', {'step': {/literal}{$checkoutStep}{literal}, 'option': '{/literal}{$sPayment.description|escape}{literal}'});
                    ga('send', 'event', 'checkout', 'step' + {/literal}{$checkoutStep}{literal});
                {/literal}
            {elseif $trackingType == "GTM"}
                {$dataLayerName|escape}.push({literal}{
                    'event': 'checkout',
                    'ecommerce': {
                      'checkout': {
                        'actionField': {'step': {/literal}{$checkoutStep}{literal}, 'option': '{/literal}{$sPayment.description|escape}{literal}'}
                     }
                   }
                  }{/literal});
            {/if}
        </noscript>
    {/block}
{/block}
