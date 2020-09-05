{extends file='parent:frontend/checkout/shipping_payment.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/checkout/shipping_payment.tpl"}
{/block}