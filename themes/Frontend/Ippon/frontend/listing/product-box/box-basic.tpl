{extends file="parent:frontend/listing/product-box/box-basic.tpl"}

{* @Dupp: add additional titles *}
{block name='frontend_listing_box_article_price_info'}
    <div class="product--title-additional">{$sArticle.articlegroup}{if $sArticle.articlegroup && $sArticle.targetgroup} | {/if}{$sArticle.targetgroup}</div>

    {$smarty.block.parent}
{/block}

{* @Dupp: remove Product price - Unit price *}
{block name='frontend_listing_box_article_unit'}{/block}