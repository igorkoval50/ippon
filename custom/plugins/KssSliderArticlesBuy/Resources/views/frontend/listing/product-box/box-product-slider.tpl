{extends file='parent:frontend/listing/product-box/box-product-slider.tpl'}


{block name="frontend_listing_box_article_buy"}
    {if $sliderInitOnEvent}
                {include file="frontend/plugins/kss_slider_articles_buy/listing/product-box/kss-button-buy.tpl"}
    {/if}
{/block}