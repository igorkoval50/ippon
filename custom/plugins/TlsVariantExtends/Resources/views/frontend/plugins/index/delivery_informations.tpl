{extends file='parent:frontend/plugins/index/delivery_informations.tpl'}

{block name='frontend_widgets_delivery_infos_not_available'}
    {if !(!$sArticle.notification && $tlsVariantExtends.noSaleMessage)}
        {$smarty.block.parent}
    {/if}
{/block}
