{extends file="parent:frontend/listing/product-box/box-basic.tpl"}

{block name="frontend_listing_box_article_actions"}
    {if !$minimalView}
        {$smarty.block.parent}
    {/if}
{/block}