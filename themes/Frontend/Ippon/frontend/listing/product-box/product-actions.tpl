{extends file="parent:frontend/listing/product-box/product-actions.tpl"}

{namespace name="frontend/listing/box_article"}

{* Product actions *}
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
