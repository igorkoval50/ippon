{extends file="parent:frontend/compare/col_description.tpl"}

{block name='frontend_compare_price'}
    {$smarty.block.parent}

    {block name='frontend_compare_price_swagcustomproducts_include'}
        {include file="frontend/swag_custom_products/compare/col_description.tpl"}
    {/block}
{/block}
