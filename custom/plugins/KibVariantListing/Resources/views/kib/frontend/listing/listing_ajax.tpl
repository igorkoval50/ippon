{extends file="parent:frontend/listing/listing_ajax.tpl"}

{block name="frontend_listing_list_inline_ajax"}
    {foreach $sArticles as $sArticle}
        {include file="frontend/plugins/kib_variant_listing/listing/box_article.tpl"}
    {/foreach}
{/block}
