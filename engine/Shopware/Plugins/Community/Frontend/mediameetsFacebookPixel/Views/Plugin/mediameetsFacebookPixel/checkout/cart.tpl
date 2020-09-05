{if isset($sArticle)}
    mmFbPixel.events.push({
        AddToCart: {
            value: {$mediameetsFacebookPixel.data.checkout.product.value|floatval},
            currency: '{$Shop->getCurrency()->getCurrency()}',
            content_name: '{$sArticle.articlename|escape:'quotes'}',
            contents: [{ldelim}'id': '{$mediameetsFacebookPixel.data.checkout.product.identifier}', 'quantity': {$sArticle.quantity|intval}{rdelim}],
            content_type: 'product'
        }
    });
{/if}
