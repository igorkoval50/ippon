{extends file="parent:frontend/checkout/items/product.tpl"}

{block name="frontend_checkout_cart_item_image_container_inner"}
    {$smarty.block.parent}

    {block name='promotion_free_goods_cart_item_badge'}
        {if $sBasketItem.isFreeGoodByPromotionId}
            <span class="cart--badge">
                <span>{$sBasketItem.freeGoodsBundleBadge}</span>
            </span>
        {/if}
    {/block}
{/block}

{block name='frontend_checkout_cart_item_quantity'}
    {if $sBasketItem.isFreeGoodByPromotionId}
        <div class="panel--td column--quantity is--align-right">

            {* Label *}
            {block name='frontend_checkout_cart_item_quantity_label'}
                <div class="column--label quantity--label">
                    {s name="CartColumnQuantity" namespace="frontend/checkout/cart_header"}{/s}
                </div>
            {/block}

            {block name='frontend_checkout_cart_item_quantity_selection'}
                {$sBasketItem.quantity}
            {/block}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
