{extends file="parent:frontend/search/ajax.tpl"}

{block name="search_ajax_list_entry"}
    {if $search_result.lenz_variants_everywhere_variantname && {config name="appendVariantNameToArticleName" namespace="LenzVariantsEverywhere"} && {config namespace="LenzVariantsEverywhere" name="showVariantsInSearch" default=true}}
        {$search_result['name'] = $search_result['name']|cat:' - '|cat:$search_result['lenz_variants_everywhere_variantname']}
    {/if}

    {$smarty.block.parent}
{/block}

{*block name="search_ajax_list_entry_name"}
    <span class="entry--name block">
        {if $search_result.lenz_variants_everywhere_variantname && {config name="appendVariantNameToArticleName" namespace="LenzVariantsEverywhere"}}
            {$lenzVariantsEverywhereVariantname = $search_result['name']|cat:' - '|cat:$search_result['lenz_variants_everywhere_variantname']}
            {$lenzVariantsEverywhereVariantname|escapeHtml}
        {else}
            {$search_result.name|escapeHtml}
        {/if}
    </span>
{/block*}
