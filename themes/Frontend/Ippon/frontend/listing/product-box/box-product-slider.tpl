{extends file="parent:frontend/listing/product-box/box-product-slider.tpl"}


{*block name="frontend_listing_box_article_buy"}
    {if {config name="displayListingBuyButton"}}
        <div class="product--btn-container">
            {if $sArticle.allowBuyInListing}
                {include file="frontend/listing/product-box/button-buy.tpl"}
            {else}
                {include file="frontend/listing/product-box/button-detail.tpl"}
            {/if}
        </div>
    {/if}
{/block*}
