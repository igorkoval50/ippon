{extends file="parent:frontend/swag_bundle/bundle/container/bundle_products.tpl"}

{block name='bundle_wrapper_article'}
    {if $product.articleName}
        {$productName = $product.articleName}
    {else}
        {$productName = $product.name}
    {/if}

    {$smarty.block.parent}
{/block}

{* product supplier and price *}
{block name='bundle_article_price_supplier'}
    <div class="bundle--product-price-supplier">
        {if $product.supplier}
            <span class="bundle--product-supplier"> - {$product.supplier}</span>
        {/if}
    </div>
{/block}