{extends file="parent:frontend/blog/listing.tpl"}

{* Blog Filter Button *}
{block name='frontend_blog_listing_filter_button'}
    {if !$sCategoryInfo.hideFilter}
        <div class="blog--filter-btn">
            {s namespace="frontend/listing/listing_actions" name="ListingFilterButton" assign="snippetListingFilterButton"}{/s}
            <a href="#"
               title="{$snippetListingFilterButton|escape}"
               class="filter--trigger btn is--icon-left"
               data-collapseTarget=".blog--filter-options"
               data-offcanvasFilter="true"
               data-offCanvasSelector=".blog--filter-options"
               data-closeButtonSelector=".blog--filter-close-btn">
                <i class="icon--filter"></i> {s namespace='frontend/listing/listing_actions' name='ListingFilterButton'}{/s}
            </a>
        </div>
    {/if}
{/block}
