{extends file="parent:frontend/search/fuzzy.tpl"}

{block name='frontend_search_info_messages'}
    {$smarty.block.parent}
    {block name="frontend_search_info_messages_swag_fuzzy"}
        {include file="frontend/swag_fuzzy/search/fuzzy_emotion.tpl"}
    {/block}
{/block}

{block name='frontend_search_headline'}
    {block name="frontend_search_headline_swag_fuzzy"}
        {foreach $facets as $facet}
            {if $facet->getFacetName() === 'keyword_facet'}
                {$keywordFacet = $facet}
            {/if}
        {/foreach}

        {if $keywordFacet}
            {include file="frontend/swag_fuzzy/search/fuzzy_result.tpl"}
        {else}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}
