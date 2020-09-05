{extends file="parent:widgets/recommendation/{$template}.tpl"}

{block name="frontend_detail_index_also_bought_slider"}
    {$smarty.block.parent}

    {if $trackingType == "UA"}
        {include file="frontend/widgets/analytics.tpl"}
    {elseif $trackingType == "GTM"}
        {include file="frontend/widgets/tag_manager.tpl"}
    {/if}
{/block}

{block name="frontend_detail_index_similar_viewed_slider"}
    {$smarty.block.parent}
    
    {if $trackingType == "UA"}
        {include file="frontend/widgets/analytics.tpl"}
    {elseif $trackingType == "GTM"}
        {include file="frontend/widgets/tag_manager.tpl"}
    {/if}
{/block}
