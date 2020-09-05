{extends file="parent:frontend/checkout/cart_item_product.tpl"}

{* Product *}
{block name='frontend_checkout_cart_item_product'}
    {$smarty.block.parent}
    {include file="frontend/swag_custom_products/checkout/product_custom_product_info.tpl"}
{/block}