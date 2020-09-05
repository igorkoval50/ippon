{extends file="parent:widgets/emotion/index.tpl"}

{* Config block for overriding configuration variables of the shopping world *}
{block name="widgets/emotion/index/config" append}
    {$baseWidth = 1260}
    {if $Controller == 'listing' && $theme.displaySidebar}
        {$baseWidth = 900}
    {/if}
{/block}

{block name="widgets/emotion/index/emotion"}
    <div class="emotion--background {$emotion.name|strip_tags:false|lower|truncate:20:''|replace:' ':'-'}">
        <div class="background--inner">
            {$smarty.block.parent}
        </div>
    </div>
{/block}