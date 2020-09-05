{extends file="parent:widgets/emotion/index.tpl"}

{* Config block for overriding configuration variables of the shopping world *}
{block name="widgets/emotion/index/config" append}
    {$baseWidth = 1260}
    {if $Controller == 'listing' && $theme.displaySidebar}
        {$baseWidth = 900}
    {/if}
{/block}

{block name="widgets/emotion/index/emotion"}
    <div class="emotion--border-bottom {$emotion.name|strip_tags:false|lower|truncate:20:''|replace:' ':'-'}">
        <div class="border-bottom">
            {$smarty.block.parent}
        </div>
    </div>
{/block}