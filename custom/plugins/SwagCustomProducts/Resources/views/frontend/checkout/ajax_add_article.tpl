{extends file="parent:frontend/checkout/ajax_add_article.tpl"}

{block name="checkout_ajax_add_information"}
    {$smarty.block.parent}

    {$IS_NON_CUSTOM_PRODUCT = 0}
    {$IS_CUSTOM_PRODUCT_MAIN = 1}
    {$IS_CUSTOM_PRODUCT_OPTION = 2}
    {$IS_CUSTOM_PRODUCT_VALUE = 3}

    {* Check if we're dealing with a custom product *}
    {if $sArticle.customProductMode == $IS_CUSTOM_PRODUCT_MAIN ||
        $sArticle.customProductMode == $IS_CUSTOM_PRODUCT_OPTION ||
        $sArticle.customProductMode == $IS_CUSTOM_PRODUCT_VALUE}

        {block name='checkout_ajax_add_information_swag_custom_products'}
            {include file="frontend/swag_custom_products/checkout/product_custom_product_info.tpl" sBasketItem = $sArticle}
        {/block}
    {/if}
{/block}
