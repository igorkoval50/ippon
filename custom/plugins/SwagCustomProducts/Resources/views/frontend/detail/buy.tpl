{extends file='parent:frontend/detail/buy.tpl'}

{block name='frontend_detail_buy_quantity'}
    {if $swagCustomProductsTemplate && $customProductsIsEmotionAdvancedQuickView}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='widgets_swag_emotion_advanced_buy_button'}
    {if $swagCustomProductsTemplate && $customProductsIsEmotionAdvancedQuickView}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
