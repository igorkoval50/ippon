{extends file='parent:frontend/_includes/product_slider_item.tpl'}

{block name="frontend_common_product_slider_item"}
    <div class="product-slider--item">
        {include file="frontend/plugins/kib_variant_listing/listing/box_article.tpl" sArticle=$article productBoxLayout=$productBoxLayout fixedImageSize=$fixedImageSize}
    </div>
{/block}
