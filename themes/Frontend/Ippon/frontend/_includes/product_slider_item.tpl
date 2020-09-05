{extends file="parent:frontend/_includes/product_slider_item.tpl"}

{block name="frontend_common_product_slider_item_config"}
    {$productBoxLayout = ($productBoxLayout) ? $productBoxLayout : ""}
    {$fixedImageSize = false}
{/block}