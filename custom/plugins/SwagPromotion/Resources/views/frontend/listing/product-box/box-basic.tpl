{extends file="parent:frontend/listing/product-box/box-basic.tpl"}

{block name="frontend_listing_box_article_buy"}
    {if {config name="displayListingBuyButton"}}
        {$detailButtonBoxTemplate = 'frontend/listing/product-box/button-detail.tpl'}

        {if $sArticle.allowBuyInListing}
            {if $sArticle.attributes.buttonTypeMode.buttonTypeMode == 'both'}
                {$detailButtonBoxTemplate = 'frontend/listing/product-box/button-both.tpl'}
            {elseif $sArticle.attributes.buttonTypeMode.buttonTypeMode == 'details'}
                {$detailButtonBoxTemplate = 'frontend/listing/product-box/button-detail.tpl'}
            {else}
                {$detailButtonBoxTemplate = 'frontend/listing/product-box/button-buy.tpl'}
            {/if}
        {/if}
        <div class="product--btn-container">
            {include file=$detailButtonBoxTemplate}
        </div>
    {/if}
{/block}
