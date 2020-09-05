{extends file='parent:frontend/forms/index.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/forms/index.tpl"}
{/block}