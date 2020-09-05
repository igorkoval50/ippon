{extends file="parent:frontend/_includes/product_slider.tpl"}

{* Config *}
{block name="frontend_common_product_slider_config"}
    {$smarty.block.parent}
    {$sliderItemMinWidth = ($sliderItemMinWidth)?$sliderItemMinWidth:"260"}
{/block}
