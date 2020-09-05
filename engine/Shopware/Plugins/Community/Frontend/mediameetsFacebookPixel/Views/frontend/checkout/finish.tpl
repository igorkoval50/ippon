{extends file='parent:frontend/checkout/finish.tpl'}

{block name="mediameetsFacebookPixelScript"}
    {$smarty.block.parent}
    {include file="mediameetsFacebookPixel/checkout/finish.tpl"}
{/block}