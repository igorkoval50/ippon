{extends file="parent:frontend/listing/listing.tpl"}

{block name="frontend_listing_listing_content"}
    <div class="listing"
         data-ajax-wishlist="true"
         data-compare-ajax="true"
         data-mobile-count="2"
         data-tablet-count="3"
         data-tabletLandscape-count="3"
            {if $theme.infiniteScrolling}
                data-infinite-scrolling="true"
                data-loadPreviousSnippet="{s name="ListingActionsLoadPrevious"}{/s}"
                data-loadMoreSnippet="{s name="ListingActionsLoadMore"}{/s}"
                data-categoryId="{$sCategoryContent.id}"
                data-pages="{$pages}"
                data-threshold="{$theme.infiniteThreshold}"
                data-pageShortParameter="{$shortParameters.sPage}"
            {/if}>

        {* Actual listing *}
        {block name="frontend_listing_list_inline"}
            {foreach $sArticles as $sArticle}
                {include file="frontend/listing/box_article.tpl"}
            {/foreach}
        {/block}
    </div>
{/block}
