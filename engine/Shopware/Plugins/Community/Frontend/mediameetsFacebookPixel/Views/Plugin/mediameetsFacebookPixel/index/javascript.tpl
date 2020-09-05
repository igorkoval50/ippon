{if isset($mediameetsFacebookPixel.config.status) && $mediameetsFacebookPixel.config.status === true}
var mmFbPixel = {$mediameetsFacebookPixel.jsConfig|json_encode};
mmFbPixel.dataController = '{url controller=facebookPixelData}';
{if $mediameetsFacebookPixel.config.advancedMatching === true && isset($mediameetsFacebookPixel.data.advancedMatchingData)}
mmFbPixel.advancedMatchingData = {$mediameetsFacebookPixel.data.advancedMatchingData|json_encode};
{/if}
mmFbPixel.events = [];
{* track CustomerHasAccount *}
{if isset($mediameetsFacebookPixel.data.customerHasAccount) && $mediameetsFacebookPixel.data.customerHasAccount === true}
mmFbPixel.events.push('CustomerHasAccount');
{/if}

{* track FrequentShopper *}
{if $mediameetsFacebookPixel.data.orders.totalOrders >= 2}
mmFbPixel.events.push({
    FrequentShopper: {
        average_order_amount: {$mediameetsFacebookPixel.data.orders.averageOrder},
        total_orders: {$mediameetsFacebookPixel.data.orders.totalOrders}
    }
});
{/if}

{* track CustomerStreams *}
{if $mediameetsFacebookPixel.config.customerStreams === true && isset($mediameetsFacebookPixel.data.customerStreams)}
mmFbPixel.events.push({
    CustomerStreams: {$mediameetsFacebookPixel.data.customerStreams|json_encode}
});
{/if}

{block name='mediameetsFacebookPixelScript'}{/block}
{/if}
