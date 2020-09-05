{extends file='parent:frontend/listing/manufacturer.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/listing/manufacturer.tpl"}
{/block}