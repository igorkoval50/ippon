{extends file="parent:frontend/blog/detail.tpl"}

{* Image + Thumbnails *}
{block name='frontend_blog_detail_images'}
    {$smarty.block.parent}

    {* Description *}
    {if $sArticle.shortDescription }
        <span class="blog--short-description">
            {$sArticle.shortDescription }
        </span>
    {/if}
{/block}

{* @Dupp: change minWidth of product slider items *}
{block name='frontend_blog_detail_crossselling_listing'}
    <div class="blog--crossselling panel--body is--wide block">
        {include file="frontend/_includes/product_slider.tpl" sliderItemMinWidth="224" articles=$sArticle.sRelatedArticles productSliderCls="crossselling--content panel--body is--rounded"}
    </div>
{/block}
