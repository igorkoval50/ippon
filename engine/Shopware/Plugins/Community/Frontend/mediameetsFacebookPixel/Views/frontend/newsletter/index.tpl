{extends file='parent:frontend/newsletter/index.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/newsletter/index.tpl"}
{/block}