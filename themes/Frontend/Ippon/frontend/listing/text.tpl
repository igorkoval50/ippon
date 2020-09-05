{extends file="parent:frontend/listing/text.tpl"}

{* Short description *}
{block name="frontend_listing_text_content_short"}
    <div class="teaser--text-short is--hidden">
        <span class="visible--mobile">{$sCategoryContent.cmstext|strip_tags|truncate:80}</span>
        <span class="visible--tablet">{$sCategoryContent.cmstext|strip_tags|truncate:200}</span>

        {s namespace="frontend/listing/listing" name="ListingActionsOpenOffCanvas" assign="snippetListingActionsOpenOffCanvas"}{/s}
        <a href="#" title="{$snippetListingActionsOpenOffCanvas|escape}" class="text--offcanvas-link">
            {s namespace="frontend/listing/listing" name="ListingActionsOpenOffCanvas"}{/s} &raquo;
        </a>
    </div>
{/block}
