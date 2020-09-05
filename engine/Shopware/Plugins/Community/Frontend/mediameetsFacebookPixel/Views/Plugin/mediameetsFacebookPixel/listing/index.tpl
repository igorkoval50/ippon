{if !isset($manufacturer)}
mmFbPixel.events.push({
    ViewCategory: {
        category_name: '{$sCategoryContent.name|escape:'quotes'}',
        category_id: '{$sCategoryContent.id}',
        category_path: '{$mediameetsFacebookPixel.data.listing.categoryPath|escape:'quotes'}',
        content_type: 'product',
        contents: {$mediameetsFacebookPixel.data.listing.contents|json_encode}
    }
});
{/if}
