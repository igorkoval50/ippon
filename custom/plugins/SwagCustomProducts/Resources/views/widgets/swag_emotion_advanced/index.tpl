{extends file='parent:widgets/swag_emotion_advanced/index.tpl'}
{namespace name='frontend/detail/hint'}

{block name='swag_emotion_advanced_quick_view_product_detail_button'}
    {$smarty.block.parent}
    {if $swagCustomProductsTemplate && $customProductsIsEmotionAdvancedQuickView}
        {block name='frontend_detail_buy_button_bundle_hint'}
            <div class="quick-view--custom-product-hint">
                {include file='frontend/_includes/messages.tpl' content="{s name=customProductQuickView}This product could be customized.{/s}" type='info'}
            </div>
        {/block}
    {/if}
{/block}
