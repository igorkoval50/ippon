var fbArticleData = {
    value: {$mediameetsFacebookPixel.data.detail.product.value|floatval},
    currency: '{$Shop->getCurrency()->getCurrency()}',
    content_name: '{$sArticle.articleName|escape:'quotes'}',
    contents: [{ldelim}'id': '{$mediameetsFacebookPixel.data.detail.product.identifier}', 'quantity': 1{rdelim}],
    content_type: 'product'
};

mmFbPixel.events.push({
    ViewContent: fbArticleData
}, {
    ViewProduct: fbArticleData
});

{if $sAction == "ratingAction" && !$sErrorFlag}
    {if isset($mediameetsFacebookPixel.data.ratedProduct.stars)}
        var ratingData = {
            stars: '{$mediameetsFacebookPixel.data.ratedProduct.stars|escape}'
        };
    {/if}
mmFbPixel.events.push({
    RatedProduct: Object.assign({ldelim}{rdelim}, fbArticleData, ratingData)
});
{/if}
