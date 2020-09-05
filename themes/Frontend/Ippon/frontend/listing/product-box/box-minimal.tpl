{extends file="parent:frontend/listing/product-box/box-minimal.tpl"}
{namespace name="frontend/listing/box_article"}

{* ... rearrange price order*}
{block name='frontend_listing_box_article_price'}
    <div class="product--price-outer">
        <div class="product--price">

            {* Default price *}
            {block name='frontend_listing_box_article_price_default'}
                <span class="price--default is--nowrap{if $sArticle.has_pseudoprice} is--discount{/if}">
                    {if $sArticle.priceStartingFrom}{s name='ListingBoxArticleStartsAt'}{/s} {/if}
                    {$sArticle.price|currency}
                    {s name="Star"}{/s}
                </span>
            {/block}

            {* Discount price *}
            {block name='frontend_listing_box_article_price_discount'}
                {if $sArticle.has_pseudoprice}
                    <span class="price--pseudo">

                        {block name='frontend_listing_box_article_price_discount_before'}
                            {s name="priceDiscountLabel" namespace="frontend/detail/data"}{/s}
                        {/block}

                        <span class="price--discount is--nowrap">
                            {$sArticle.pseudoprice|currency}
                            {s name="Star"}{/s}
                        </span>

                        {block name='frontend_listing_box_article_price_discount_after'}
                            {s name="priceDiscountInfo" namespace="frontend/detail/data"}{/s}
                        {/block}
                    </span>
                {/if}
            {/block}

        </div>
    </div>
{/block}

{* Product box badges - highlight, newcomer, ESD product and discount *}
{block name='frontend_listing_box_article_info_container'}
    {block name='frontend_listing_box_article_actions_content'}
        <div class="product--actions">

            {* Compare button *}
            {block name='frontend_listing_box_article_actions_compare'}{/block}

            {* Note button *}
            {block name='frontend_listing_box_article_actions_save'}
                <form action="{url controller='note' action='add' ordernumber=$sArticle.ordernumber _seo=false}" method="post">
                    {s name="DetailLinkNotepad" namespace="frontend/detail/actions" assign="snippetDetailLinkNotepad"}{/s}
                    <button type="submit"
                            title="{$snippetDetailLinkNotepad|escape}"
                            aria-label="{$snippetDetailLinkNotepad|escape}"
                            class="product--action action--note"
                            data-ajaxUrl="{url controller='note' action='ajaxAdd' ordernumber=$sArticle.ordernumber _seo=false}"
                            data-text="{s name="DetailNotepadMarked"}{/s}">
                        <i class="icon--heart"></i>
                    </button>
                </form>
            {/block}
        </div>
    {/block}
    {$smarty.block.parent}
    {* Product actions *}

{/block}

