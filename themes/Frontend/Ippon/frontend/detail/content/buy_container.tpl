{extends file="parent:frontend/detail/content/buy_container.tpl"}

{* @Dupp: move Product email notification *}
{* @Dupp: move swarcustomproducts container *}
{* @Dupp: add tablesizes *}
{block name="frontend_detail_index_notification"}{/block}
{block name='frontend_detail_buy_laststock'}{/block}

{block name="frontend_detail_index_configurator"}
    <div class="product--configurator">
        {if $sArticle.sConfigurator}
            {if $sArticle.sConfiguratorSettings.type == 1}
                {$file = 'frontend/detail/config_step.tpl'}
            {elseif $sArticle.sConfiguratorSettings.type == 2}
                {$file = 'frontend/detail/config_variant.tpl'}
            {else}
                {$file = 'frontend/detail/config_upprice.tpl'}
            {/if}
            {include file=$file}
        {/if}
    </div>

    {* Size Chart information *}
    {block name='frontend_detail_data_sizechart'}
        {*if $sArticle.sizechart}
            <p class="product--size-charts" data-content="" data-modalbox="true" data-targetSelector="a" data-mode="ajax">
                {s name='DetailBuyInfoSizeCharts' namespace='frontend/detail/buy' assign="sizeChartTitle"}Größentabelle ansehen{/s}
                <a title="{$sizeChartTitle}" href="{url controller=custom sCustom={$sArticle.sizechart}}">
                    <i class="theme-icon--info"></i>
                    <span class="size-charts--title">{$sizeChartTitle}</span>
                </a>
            </p>
        {/if*}
    {/block}

    {* Custom products frontend hook *}
    {block name="frontend_detail_data_swagcustomproducts"}
        {if $swagCustomProductsTemplate && !$customProductsIsEmotionAdvancedQuickView}
            {include file="frontend/swag_custom_products/detail/wrapper.tpl"}
        {/if}
    {/block}

    {block name='frontend_detail_buy_laststock_new'}
        {if !$sArticle.isAvailable && !$sArticle.sConfigurator}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='DetailBuyInfoNotAvailable' namespace='frontend/detail/buy'}{/s}"}

        {elseif !$sArticle.isAvailable && $sArticle.isSelectionSpecified}
            {if !$sArticle.notification && $tlsVariantExtends.noSaleMessage}
                {include file="frontend/_includes/messages.tpl" type="error" content="{s name="DetailBuyInfoNoLongerAvailable" namespace="frontend/plugins/tls_variant_extends/detail"}{/s}"}
            {else}
                {include file="frontend/_includes/messages.tpl" type="error" content="{s name='DetailBuyInfoNotAvailable' namespace='frontend/detail/buy'}{/s}"}
            {/if}

        {elseif !$sArticle.isAvailable && !$sArticle.hasAvailableVariant}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='DetailBuyInfoNotAvailable' namespace='frontend/detail/buy'}{/s}"}
        {/if}
    {/block}

    {block name="frontend_detail_index_notification_new"}
        {if $ShowNotification && $sArticle.notification && $sArticle.instock < $sArticle.minpurchase}
            {* Support products with or without variants *}
            {if ($sArticle.hasAvailableVariant && ($sArticle.isSelectionSpecified || !$sArticle.sConfigurator)) || !$sArticle.hasAvailableVariant}
                {include file="frontend/plugins/notification/index.tpl"}
            {/if}
        {/if}
    {/block}

{/block}

{block name='frontend_detail_index_data'}
    {if $sArticle.description}
        <div class="product--description-top">
            <p>{$sArticle.description}</p>
        </div>
    {/if}

    {$smarty.block.parent}
{/block}

{* Product actions *}
{block name="frontend_detail_index_actions"}
    <nav class="product--actions">
        {include file="frontend/detail/actions.tpl"}
    </nav>
{/block}

{* Product - Base information *}
{block name='frontend_detail_index_buy_container_base_info'}{/block}