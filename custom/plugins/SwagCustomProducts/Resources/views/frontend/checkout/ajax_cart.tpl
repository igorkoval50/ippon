{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_row'}

    {$IS_NON_CUSTOM_PRODUCT = 0}
    {$IS_CUSTOM_PRODUCT_MAIN = 1}
    {$IS_CUSTOM_PRODUCT_OPTION = 2}
    {$IS_CUSTOM_PRODUCT_VALUE = 3}

    {* Check if we're dealing with a custom product *}
    {if $sBasketItem.customProductMode == $IS_CUSTOM_PRODUCT_MAIN ||
        $sBasketItem.customProductMode == $IS_CUSTOM_PRODUCT_OPTION ||
        $sBasketItem.customProductMode == $IS_CUSTOM_PRODUCT_VALUE}

        {block name='frontend_checkout_ajax_cart_row_swag_custom_products'}
            {include file="frontend/swag_custom_products/checkout/ajax_cart.tpl"}
        {/block}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
