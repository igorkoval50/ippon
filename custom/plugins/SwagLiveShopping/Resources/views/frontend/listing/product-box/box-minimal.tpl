{extends file="parent:frontend/listing/product-box/box-minimal.tpl"}

{* Liveshopping price *}
{block name='frontend_listing_box_article_price'}
    {if $sArticle.liveShopping}
        {include file='frontend/swag_live_shopping/listing/liveshopping-listing-pricing.tpl'}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
