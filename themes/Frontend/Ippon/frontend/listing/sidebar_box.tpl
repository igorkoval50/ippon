{block name="frontend_listing_sidebar_box"}
    {if $sCategoryContent.attribute.cmsheadline || $sCategoryContent.attribute.cmscontent}
        <div class="hero-unit category--teaser panel has--border is--rounded">
            <h2 class="hero--headline panel--title">
                {$sCategoryContent.attribute.cmsheadline}
            </h2>
            <div class="hero--text panel--body is--wide">
                {$sCategoryContent.attribute.cmscontent}
            </div>
        </div>
    {/if}
{/block}