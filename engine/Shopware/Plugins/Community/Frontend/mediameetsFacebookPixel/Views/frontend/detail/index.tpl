{extends file='parent:frontend/detail/index.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/detail/index.tpl"}
{/block}