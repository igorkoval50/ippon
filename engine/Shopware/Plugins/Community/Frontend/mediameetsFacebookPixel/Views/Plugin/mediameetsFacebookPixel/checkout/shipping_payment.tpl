{if isset($mediameetsFacebookPixel.data.basket)}
mmFbPixel.events.push({
    AddPaymentInfo: {
        value: {$mediameetsFacebookPixel.data.basket.value|floatval},
        currency: '{$Shop->getCurrency()->getCurrency()}',
        content_type: 'product',
        contents: {$mediameetsFacebookPixel.data.basket.contents|json_encode}
    }
});
{/if}
