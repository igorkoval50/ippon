<script>
    var mmFbPixel = {
        mode: '{$mediameetsFacebookPixel.config.privacyMode}',
        id: '{$mediameetsFacebookPixel.config.facebookPixelID|escape}',
        additionalIds: {$mediameetsFacebookPixel.config.additionalFacebookPixelIDs},
        shopId: '{$mediameetsFacebookPixel.config.shopId}',
        dataController: '{url controller=facebookPixelData}',
        autoConfig: {$mediameetsFacebookPixel.config.autoConfig|intval},
        swCookieMode: {$mediameetsFacebookPixel.config.swCookieMode|intval},
        swCookieDisplay: {$mediameetsFacebookPixel.config.swCookieDisplay|intval},
        {if $mediameetsFacebookPixel.config.advancedMatching === true && isset($mediameetsFacebookPixel.advancedMatchingData)}
        advancedMatchingData: {$mediameetsFacebookPixel.advancedMatchingData},
        {/if}
        events: []
    };

    {* track CustomerHasAccount *}
    {if isset($mediameetsFacebookPixel.customerHasAccount) && $mediameetsFacebookPixel.customerHasAccount === true}
    mmFbPixel.events.push('CustomerHasAccount');
    {/if}

    {* track FrequentShopper *}
    {if $mediameetsFacebookPixel.orders.totalOrders >= 2}
    mmFbPixel.events.push({
        FrequentShopper: {
            average_order_amount: {$mediameetsFacebookPixel.orders.averageOrder},
            total_orders: {$mediameetsFacebookPixel.orders.totalOrders}
        }
    });
    {/if}

    {* track CustomerStreams *}
    {if $mediameetsFacebookPixel.config.customerStreams === true && isset($mediameetsFacebookPixel.customerStreams)}
    mmFbPixel.events.push({
        CustomerStreams: {$mediameetsFacebookPixel.customerStreams}
    });
    {/if}

    {block name='mediameetsFacebookPixelScript'}{/block}

    {if ! $theme.asyncJavascriptLoading}
    window.StateManager.addPlugin('body', 'mmFbPixel', $.extend({}, mmFbPixel));
    {/if}
</script>