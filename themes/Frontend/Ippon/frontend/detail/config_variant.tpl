{extends file="parent:frontend/detail/config_variant.tpl"}

{block name='frontend_detail_configurator_variant_group_name'}
    <p class="variant--name">{$configuratorGroup.groupname}
        {* Size Chart information *}
        {block name='frontend_detail_data_sizechart'}
            {if $configuratorGroup['groupID']  == 2}
                {if $sArticle.sizechart}
                    <span class="product--size-charts right" data-content="" data-modalbox="true"
                          data-targetSelector="a" data-mode="ajax">
                        {s name='DetailBuyInfoSizeCharts' namespace='frontend/detail/buy' assign="sizeChartTitle"}Größentabelle ansehen{/s}
                        <a title="{$sizeChartTitle}" href="{url controller=custom sCustom={$sArticle.sizechart}}">
                            <span class="size-charts--title">{$sizeChartTitle}</span>
                        </a>
                    </span>
                {/if}
            {/if}
        {/block}
    </p>
{/block}

{block name='frontend_detail_configurator_variant_group_option_label_image'}
    <span class="image--element">
        <span class="image--media">
            {if isset($media.thumbnails)}
                <img class="lazyLoad" data-srcset="{$media.thumbnails[0].sourceSet}" alt="{$option.optionname}" />
            {else}
                <img class="lazyLoad" data-src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$option.optionname}">
            {/if}
        </span>
    </span>
{/block}