{$IS_NON_CUSTOM_PRODUCT = 0}
{$IS_CUSTOM_PRODUCT_MAIN = 1}
{$IS_CUSTOM_PRODUCT_OPTION = 2}
{$IS_CUSTOM_PRODUCT_VALUE = 3}

{if $sBasketItem.customProductMode == $IS_NON_CUSTOM_PRODUCT || $sBasketItem.customProductMode == $IS_CUSTOM_PRODUCT_MAIN}
    {block name=frontend_detail_custom_products_custom_cart_item}
    <div class="custom-product--cart-item">
        {* Include the default template *}
        {include file="frontend/checkout/ajax_cart_item.tpl" basketItem=$sBasketItem}

        {* ...and include over custom product information *}
        {include file="frontend/swag_custom_products/checkout/product_custom_product_info.tpl"}
    </div>
    {/block}
{/if}
