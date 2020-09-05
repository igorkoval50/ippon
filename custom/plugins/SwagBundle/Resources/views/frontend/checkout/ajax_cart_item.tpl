{namespace name="frontend/checkout/ajax_cart"}

{extends file='parent:frontend/checkout/ajax_cart_item.tpl'}

{* Rebate article *}
{block name='frontend_checkout_ajax_cart_articleimage_badge_rebate'}
    {if $basketItem.modus == $IS_REBATE || $sBasketItem.modus == constant('\SwagBundle\Components\BundleBasketInterface::BUNDLE_DISCOUNT_BASKET_MODE')}
        <div class="basket--badge">
            {if $sBasketItem.price >= 0}
                <i class="icon--arrow-right"></i>
            {else}
                <i class="icon--percent2"></i>
            {/if}
        </div>
    {/if}
{/block}

{* Article actions *}
{block name='frontend_checkout_ajax_cart_actions'}
    {if $sBasketItem.modus == constant('\SwagBundle\Components\BundleBasketInterface::BUNDLE_DISCOUNT_BASKET_MODE')}
        <div class="action--container"></div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{* Article name *}
{block name='frontend_checkout_ajax_cart_articlename'}
    {if $sBasketItem.modus == constant('\SwagBundle\Components\BundleBasketInterface::BUNDLE_DISCOUNT_BASKET_MODE')}
        <div class="item--link">
            {block name="frontend_checkout_ajax_cart_articlename_quantity"}
                <span class="item--quantity">{$basketItem.quantity}{s name="AjaxCartQuantityTimes"}{/s}</span>
            {/block}
            {block name="frontend_checkout_ajax_cart_articlename_name"}
                <span class="item--name">
                    {if $theme.offcanvasCart}
                        {$basketItem.articlename|escapeHtml}
                    {else}
                        {$basketItem.articlename|truncate:28:"...":true|escapeHtml}
                    {/if}
                </span>
            {/block}
            {block name="frontend_checkout_ajax_cart_articlename_price"}
                <span class="item--price">{if $basketItem.amount}{$basketItem.amount|currency}{else}{s name="AjaxCartInfoFree"}{/s}{/if}{s name="Star"}{/s}</span>
            {/block}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}