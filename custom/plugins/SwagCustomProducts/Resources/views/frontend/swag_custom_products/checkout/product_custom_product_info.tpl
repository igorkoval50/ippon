{namespace name="frontend/detail/option"}

{$IS_NON_CUSTOM_PRODUCT = 0}
{$IS_CUSTOM_PRODUCT_MAIN = 1}
{$IS_CUSTOM_PRODUCT_OPTION = 2}
{$IS_CUSTOM_PRODUCT_VALUE = 3}

{* Check if we're dealing with a custom product *}
{if $sBasketItem.customProductMode == $IS_CUSTOM_PRODUCT_MAIN}
    {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges"}
        <div class="custom-products--basket-overview">

            {* Surcharges headline *}
            {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_title"}
                <h4 class="custom-product--overview-title">
                    {s name="basket/surcharges"}Surcharges{/s}:

                    {strip}<span class="custom-product--price-surcharges">{$sBasketItem.custom_product_prices.surchargesTotal|currency}
                    {if !$isCheckoutConfirm}{s name="Star" namespace="frontend/listing/box_article"}{/s}{/if}</span>{/strip}
                </h4>
            {/block}

            {* Option list *}
            {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_list"}
                <ul class="custom-product--overview-list">
                    {foreach $sBasketItem.custom_product_adds as $option}

                        {* Option *}
                        {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_list_item"}
                            <li class="custom-product--overview-list-item">

                                {* Option name *}
                                {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_item_name"}
                                    <span class="custom-product--overview-option-name">
                                        <span class="overview-option-name--name">
                                            {$option.name}
                                        </span>

                                        {if $option.could_contain_values}
                                            {* Option price *}
                                            {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_option_price"}
                                                {include file="frontend/swag_custom_products/checkout/checkout_price.tpl"
                                                    price = $option.surcharge
                                                    is_once_surcharge = $option.is_once_surcharge
                                                    isTaxFreeDelivery = $option.isTaxFreeDelivery
                                                    isCheckoutConfirm = $isCheckoutConfirm
                                                    sUserData = $sUserData
                                                    tax = $option.tax
                                                }
                                            {/block}
                                        {/if}
                                        :
                                    </span>
                                {/block}

                                {* Option value *}
                                {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_item_value"}
                                    <span class="custom-product--overview-option-value">
                                        {if $option.could_contain_values}
                                            <ul class="custom-product--value-list">
                                                {foreach $option.values as $value}
                                                    <li class="custom-product--value-list-item">
                                                        {if $value.path}<i class="icon--paperclip"></i>&nbsp;{/if}{$value.name|truncate:30}

                                                        {* Option price *}
                                                        {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_values_price"}
                                                            {include file="frontend/swag_custom_products/checkout/checkout_price.tpl"
                                                                price = $value.surcharge
                                                                is_once_surcharge = $value.is_once_surcharge
                                                                isTaxFreeDelivery = $value.isTaxFreeDelivery
                                                                isCheckoutConfirm = $isCheckoutConfirm
                                                                sUserData = $sUserData
                                                                tax = $value.tax
                                                            }
                                                        {/block}
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        {else}
                                            {if $option.type === 'date'}
                                                {$option.selectedValue.0|date:DATE_MEDIUM}
                                            {else}
                                                {$option.selectedValue.0|truncate:25}
                                            {/if}

                                            {* Option price *}
                                            {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_value_price"}
                                                {include file="frontend/swag_custom_products/checkout/checkout_price.tpl"
                                                    price = $option.surcharge
                                                    is_once_surcharge = $option.is_once_surcharge
                                                    isTaxFreeDelivery = $option.isTaxFreeDelivery
                                                    isCheckoutConfirm = $isCheckoutConfirm
                                                    sUserData = $sUserData
                                                    tax = $option.tax
                                                }
                                            {/block}
                                        {/if}
                                    </span>
                                {/block}
                            </li>
                        {/block}
                    {/foreach}
                </ul>
            {/block}

            {block name="frontend_checkout_cart_item_details_inline_swag_custom_products_surcharges_total"}
                <div class="block-group">
                    {if {controllerAction|lower} == 'cart' || {controllerAction|lower} == 'confirm'}
                        {if $sBasketItem.additional_details.sConfigurator}
                            {$detailLink={url controller=detail sArticle=$sBasketItem.articleID number=$sBasketItem.ordernumber}}
                        {else}
                            {$detailLink = {url controller=detail sArticle=$sBasketItem.articleID}}
                        {/if}
                        <div class="block custom-product--action-open-config">
                            <a href="{$detailLink}#{$sBasketItem.customProductHash}" title="{s name="basket/open_configuration"}{/s}" class="custom-product--action-open-config-link">
                                {s name="basket/open_configuration"}{/s} <i class="icon--arrow-right"></i>
                            </a>
                        </div>
                    {/if}

                    <div class="custom-products--surcharges-total block">
                        {s name="basket/total_sum"}Total sum{/s}:&nbsp;
                        <span class="surcharges-total--display">
                            {$sBasketItem.custom_product_prices.total|currency}{if {controllerAction|lower} !== 'confirm'}{/if}{strip}
                            {if !$isCheckoutConfirm}
                                {s name="Star" namespace="frontend/listing/box_article"}{/s}
                            {/if}{/strip}
                        </span>
                    </div>
                </div>
            {/block}
        </div>
    {/block}
{/if}
