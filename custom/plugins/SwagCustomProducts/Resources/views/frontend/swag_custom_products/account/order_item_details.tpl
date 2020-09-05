{namespace name="frontend/account/order_item"}

{block name='frontend_account_order_item_detail_table_row_content_swag_custom_products'}
    <div class="custom-product-panel--tr custom-product-mode--{$customProductMode}">
        <div class="panel--td">
            {* Product name *}
            <span class="custom-product--product-name">
                {if $customProductMode == 2}
                    <strong>{$article.name}</strong>
                {else}
                    {$article.name}
                {/if}

                {strip}
                    (+ {$article.quantity} x {$article.price|currency}
                    {s name="Star" namespace="frontend/listing/box_article"}{/s})
                {/strip}
            </span>
        </div>
    </div>
{/block}
