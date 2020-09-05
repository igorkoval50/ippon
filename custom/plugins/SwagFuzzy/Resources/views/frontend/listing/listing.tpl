{extends file="parent:frontend/listing/listing.tpl"}

{block name="frontend_listing_listing_wrapper"}
    {if $Controller|lower === 'search'}
        {* always show the listing regardless of the emotion settings *}
        {$showListing = true}
    {/if}
    {$smarty.block.parent}
{/block}
