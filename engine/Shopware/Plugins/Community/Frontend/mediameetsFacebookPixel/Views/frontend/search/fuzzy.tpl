{extends file='parent:frontend/search/fuzzy.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/search/fuzzy.tpl"}
{/block}