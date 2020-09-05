{extends file='parent:frontend/detail/content/header.tpl'}

{block name='frontend_detail_index_header'}
    <header class="product--header">
        {block name='frontend_detail_index_header_inner'}
            <div class="product--info">
                {block name='frontend_detail_index_product_info'}

                    {* Product - Supplier information *}
                    {block name='frontend_detail_supplier_info'}
                    {/block}

                    {* Product name *}
                    {block name='frontend_detail_index_name'}
                        <h1 class="product--title" itemprop="name">
                            {$sArticle.articleName} {$sArticle.additionaltext}
                        </h1>
                    {/block}

                    {* Product name *}
                    {block name='frontend_detail_index_name_additional'}
                        {if $sArticle.articlegroup || $sArticle.targetgroup}<div class="product--title-additional">{$sArticle.articlegroup}{if $sArticle.articlegroup && $sArticle.targetgroup} | {/if}{$sArticle.targetgroup}</div>{/if}
                    {/block}

                    {* Product rating *}
                    {block name="frontend_detail_comments_overview"}
                        {if !{config name=VoteDisable}}
                            <div class="product--rating-container">
                                <a href="#product--publish-comment" class="product--rating-link" rel="nofollow" title="{"{s namespace="frontend/detail/actions" name='DetailLinkReview'}{/s}"|escape}">
                                    {include file='frontend/_includes/rating.tpl' points=$sArticle.sVoteAverage.average type="aggregated" count=$sArticle.sVoteAverage.count}
                                </a>
                            </div>
                        {/if}
                    {/block}

                {/block}
            </div>
        {/block}
    </header>
{/block}

