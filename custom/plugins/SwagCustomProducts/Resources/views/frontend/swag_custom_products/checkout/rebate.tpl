{$IS_NON_CUSTOM_PRODUCT = 0}
{$IS_CUSTOM_PRODUCT_MAIN = 1}
{$IS_CUSTOM_PRODUCT_OPTION = 2}

{if $sBasketItem.customProductMode == $IS_NON_CUSTOM_PRODUCT}
    {include file="frontend/checkout/items/rebate.tpl" isLast=$isLast}
{/if}
