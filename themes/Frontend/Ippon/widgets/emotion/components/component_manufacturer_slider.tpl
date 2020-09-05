{extends file="parent:widgets/emotion/components/component_manufacturer_slider.tpl"}

{block name="frontend_widgets_manufacturer_slider_item_image"}
    <img class="manufacturer--image lazyLoad" data-src="{$supplier.image}" alt="{$supplier.name|escape}" />
{/block}
