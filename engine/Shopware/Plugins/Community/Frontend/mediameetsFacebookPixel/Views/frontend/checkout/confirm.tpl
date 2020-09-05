{extends file='parent:frontend/checkout/confirm.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/checkout/confirm.tpl"}
{/block}