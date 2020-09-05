{extends file='parent:frontend/detail/content/buy_container.tpl'}

{block name="frontend_detail_index_configurator"}
    {block name="frontend_detail_data_swagcustomproducts"}
        {if !$customProductsIsEmotionAdvancedQuickView}
            {if $variantsOnTop}
                {$smarty.block.parent}
            {/if}

            {include file="frontend/swag_custom_products/detail/wrapper.tpl"}

            {if !$variantsOnTop}
                {$smarty.block.parent}
            {/if}
        {/if}
    {/block}
{/block}