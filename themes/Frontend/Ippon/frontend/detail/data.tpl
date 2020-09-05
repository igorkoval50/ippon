{extends file="parent:frontend/detail/data.tpl"}

{* ... remove Delivery informations here *}
{block name="frontend_detail_data_delivery"}
    {$smarty.block.parent}

    {if $theme.showStock}
        {if ($sArticle.sConfiguratorSettings.type != 1 && $sArticle.sConfiguratorSettings.type != 2) || $activeConfiguratorSelection == true}
            {include file="frontend/plugins/index/instock_informations.tpl" sArticle=$sArticle}
        {/if}
    {/if}
{/block}

{* remove Custom products frontend hook *}
{block name="frontend_detail_data_swagcustomproducts"}{/block}