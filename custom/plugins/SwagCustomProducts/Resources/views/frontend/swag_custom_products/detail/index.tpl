{if $customProductNeedsConfig}
    {$content = "{s name='customProductNeedsConfig' namespace='frontend/detail/hint'}This product needs customisation before it could be added to the basket.{/s}"}
    {block name="frontend_detail_swag_custom_products_configuration_hint"}
        <div class="custom-products--configuration-hint">
            {include file="frontend/_includes/messages.tpl" type="info"}
        </div>
    {/block}
{/if}
