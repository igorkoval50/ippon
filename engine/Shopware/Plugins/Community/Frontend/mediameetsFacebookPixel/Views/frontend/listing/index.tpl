{extends file='parent:frontend/listing/index.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/listing/index.tpl"}
{/block}