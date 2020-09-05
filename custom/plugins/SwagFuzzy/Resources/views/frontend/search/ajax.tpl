{extends file="parent:frontend/search/ajax.tpl"}

{block name="search_ajax_inner"}
    {block name="search_ajax_inner_swag_fuzzy"}
        {include file="frontend/swag_fuzzy/search/ajax.tpl"}
    {/block}
{/block}
