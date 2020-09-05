{extends file="parent:frontend/listing/listing_actions.tpl"}

{* Listing actions *}
{block name='frontend_listing_actions_top'}
    {$listingMode = {config name=listingMode}}

    {block name="frontend_listing_actions_top_hide_detection"}
        {$class = 'listing--actions is--rounded'}

        {if !$sCategoryContent.cmsheadline && !$sCategoryContent.cmstext}
            {$class = "{$class} without--teaser"}
        {/if}

        {if ($sCategoryContent.hide_sortings || $sortings|count == 0)}
            {$class = "{$class} without-sortings"}
        {/if}

        {if ($theme.sidebarFilter || $facets|count == 0)}
            {$class = "{$class} without-facets"}
        {/if}

        {if $theme.infiniteScrolling}
            {$class = "{$class} without-pagination"}
        {/if}
    {/block}

    <div data-listing-actions="true"
         {if $listingMode != 'full_page_reload'}data-bufferTime="0"{/if}
         class="{$class}{block name='frontend_listing_actions_class'}{/block}">

        {* Filter action button *}
        {block name="frontend_listing_actions_filter"}
            {include file="frontend/listing/actions/action-filter-button.tpl"}
        {/block}


        {* Order by selection *}
        {block name='frontend_listing_actions_sort'}
            {include file="frontend/listing/actions/action-sorting.tpl"}
        {/block}

        {block name="frontend_listing_actions_filter"}
            <div class="action--count-btn">
                <div class="mobile-wrapper">
                    <div class="count-btn simple-box" data-box-type="mobile" data-box-count="1"></div>
                    <div class="count-btn multiple-box" data-box-type="mobile" data-box-count="2"></div>
                </div>

                <div class="tablet-wrapper">
                    <div class="count-btn simple-box" data-box-type="tablet" data-box-count="2"></div>
                    <div class="count-btn multiple-box" data-box-type="tablet" data-box-count="3"></div>
                </div>

                <div class="tabletLandscape-wrapper">
                    <div class="count-btn simple-box" data-box-type="tabletLandscape" data-box-count="3"></div>
                    <div class="count-btn multiple-box" data-box-type="tabletLandscape" data-box-count="4"></div>
                </div>
            </div>
        {/block}

        {* Filter options *}
        {block name="frontend_listing_actions_filter_options"}
            {if !$theme.sidebarFilter}
                {include file="frontend/listing/actions/action-filter-panel.tpl"}
            {/if}
        {/block}

        {* Listing pagination *}
        {block name='frontend_listing_actions_paging'}
            {include file="frontend/listing/actions/action-pagination.tpl"}
        {/block}

        {block name="frontend_listing_actions_close"}{/block}


    </div>
{/block}
