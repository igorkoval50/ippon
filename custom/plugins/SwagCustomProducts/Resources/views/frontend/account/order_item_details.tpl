{extends file="parent:frontend/account/order_item_details.tpl"}

{block name="frontend_account_order_item_detail_table_row"}
    {block name="frontend_account_order_item_detail_table_row_swag_custom_products"}
        {$attributes = $article.attribute}
        {if ($attributes.swag_custom_products_mode == 2 || $attributes.swag_custom_products_mode == 3) && $attributes.swag_custom_products_configuration_hash}
            {include file="frontend/swag_custom_products/account/order_item_details.tpl" customProductMode = $attributes.swag_custom_products_mode}
        {else}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}

{block name='frontend_account_order_item_currentprice'}
    {$smarty.block.parent}

    {block name='frontend_account_order_item_currentprice_swag_custom_products'}
        {$attributes = $article.attribute}
        {if $attributes.swag_custom_products_mode == 1 && $attributes.swag_custom_products_configuration_hash}
            {$detailLink = {url controller=detail sArticle=$article.articleID}}

            <div class="block custom-product--action-open-config">
                <a href="{$detailLink}#{$attributes.swag_custom_products_configuration_hash}" title="{s name="basket/open_configuration"}{/s}" class="custom-product--action-open-config-link">
                    {s name="basket/open_configuration" namespace="frontend/detail/option"}{/s} <i class="icon--arrow-right"></i>
                </a>
            </div>
        {/if}
    {/block}
{/block}

{block name='frontend_account_order_item_amount_value'}
    {$smarty.block.parent}

    {block name='frontend_account_order_item_amount_value_swag_custom_products'}
        {$attributes = $article.attribute}
        {if $attributes.swag_custom_products_mode == 1 && $attributes.swag_custom_products_configuration_hash}
            <div class="block custom-product--product-amount">
                {s namespace='frontend/detail/option' name='basket/total_sum'}Total sum{/s}: {$attributes.custom_products_amount|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
            </div>
        {/if}
    {/block}
{/block}
