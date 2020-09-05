{if isset($mediameetsFacebookPixel.data.basket)}
    mmFbPixel.events.push({
        InitiateCheckout: {
            value: {$mediameetsFacebookPixel.data.basket.value|floatval},
            currency: '{$Shop->getCurrency()->getCurrency()}',
            content_type: 'product',
            num_items: {$mediameetsFacebookPixel.data.basket.contents|@count},
            contents: {$mediameetsFacebookPixel.data.basket.contents|json_encode}
        }
    });

    {if $sRegisterFinished === true}
        mmFbPixel.events.push({
            CompleteRegistration: {
                value: {$mediameetsFacebookPixel.data.basket.value|floatval},
                currency: '{$Shop->getCurrency()->getCurrency()}'
            }
        });
    {/if}
{/if}
