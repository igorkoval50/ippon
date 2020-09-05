{extends file="parent:frontend/checkout/items/product.tpl"}

{block name='frontend_checkout_cart_item_quantity_selection'}
    {if !$sBasketItem.additional_details.laststock || ($sBasketItem.additional_details.laststock && $sBasketItem.additional_details.instock > 0)}
        <form name="basket_change_quantity{$sBasketItem.id}" class="basket_change_quantity_form" method="post" action="{url action='changeQuantity' sTargetAction=$sTargetAction}">

            {$maxQuantity=$sBasketItem.maxpurchase+1}
            {if $sBasketItem.laststock && $sBasketItem.instock < $sBasketItem.maxpurchase}
                {$maxQuantity=$sBasketItem.instock+1}
            {/if}
            <div class="product--quantity-block" data-quantitySelect="true">
                <span class="quantity--minus">
                    <i class="icon--minus3"></i>
                </span>
                <span class="quantity--select-wrapper {if $sBasketItem.additional_details.kss_product_quantity_input}is--disabled{/if}">
                <input id="sQuantity"
                       data-step="{$sBasketItem.purchasesteps}"
                       data-start="{$sBasketItem.minpurchase}"
                       data-finish="{$maxQuantity}"
                       name="sQuantity"
                       autocomplete="off"
                       class="quantity--select {if $sBasketItem.additional_details.kss_product_quantity_input}is--disabled{/if}"
                       value="{$sBasketItem.quantity}"
                       type="number">
                    </span>
                <span class="quantity--plus">
                    <i class="icon--plus3"></i>
                </span>
                <span class="quantity--submit">
                    <i class="icon--cycle"></i>
                </span>
            </div>
            <input type="hidden" name="sArticle" value="{$sBasketItem.id}" />
        </form>
    {else}
        {s name="CartColumnQuantityEmpty" namespace="frontend/checkout/cart_item"}{/s}
    {/if}
{/block}

{*{block name="frontend_checkout_cart_item_image_container_inner"}*}

{*    {$image = $sBasketItem.additional_details.image}*}
{*    {$desc = $sBasketItem.articlename|escape}*}

{*    {if $image.thumbnails[0]}*}
{*        <a href="{$detailLink}" title="{$sBasketItem.articlename|strip_tags|escape}" class="table--media-link"*}
{*                {if {config name=detailmodal} && {controllerAction|lower} === 'confirm'}*}
{*            data-modalbox="true"*}
{*            data-content="{url controller="detail" action="productQuickView" ordernumber="{$sBasketItem.ordernumber}" fullPath}"*}
{*            data-mode="ajax"*}
{*            data-width="750"*}
{*            data-sizing="content"*}
{*            data-title="{$sBasketItem.articlename|strip_tags|escape}"*}
{*            data-updateImages="true"*}
{*                {/if}>*}

{*            {if $image.description}*}
{*                {$desc = $image.description|escape}*}
{*            {/if}*}

{*            <img class="lazyLoad" data-srcset="{$image.thumbnails[0].sourceSet}" alt="{$desc}" title="{$desc|truncate:160}" />*}
{*        </a>*}
{*    {else}*}
{*        <img class="lazyLoad" data-src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$desc}" title="{$desc|truncate:160}" />*}
{*    {/if}*}
{*{/block}*}