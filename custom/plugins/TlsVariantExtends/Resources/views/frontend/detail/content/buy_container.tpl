{extends file='parent:frontend/detail/content/buy_container.tpl'}

{block name='frontend_detail_buy_laststock'}
    {if !$sArticle.notification && $tlsVariantExtends.noSaleMessage}
        {s name="DetailBuyInfoNotAvailable" namespace="frontend/detail/buy" assign="snippetDetailBuyInfoNotAvailable"}{/s}
        {s name="DetailBuyInfoNoLongerAvailable" namespace="frontend/plugins/tls_variant_extends/detail" assign="snippetDetailBuyInfoNoLongerAvailable"}{/s}

        {if !$sArticle.isAvailable && !$sArticle.sConfigurator}
            {include file="frontend/_includes/messages.tpl" type="error" content=$snippetDetailBuyInfoNotAvailable}
        {elseif !$sArticle.isAvailable && $sArticle.isSelectionSpecified}
            {include file="frontend/_includes/messages.tpl" type="error" content=$snippetDetailBuyInfoNoLongerAvailable}
        {elseif !$sArticle.isAvailable && !$sArticle.hasAvailableVariant}
            {include file="frontend/_includes/messages.tpl" type="error" content=$snippetDetailBuyInfoNotAvailable}
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
