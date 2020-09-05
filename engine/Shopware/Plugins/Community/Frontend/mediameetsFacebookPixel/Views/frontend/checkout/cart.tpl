{extends file='parent:frontend/checkout/cart.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/checkout/cart.tpl"}
{/block}