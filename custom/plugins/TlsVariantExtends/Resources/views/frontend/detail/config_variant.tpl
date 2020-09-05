{extends file="parent:frontend/detail/config_variant.tpl"}

{block name='frontend_detail_configurator_variant'}
    {$onlyOneGroup = count($configurator) === 1}
    {if $onlyOneGroup }
        {$numberGroup = 1}
    {else}
        {$numberGroup = 0}
    {/if}
    {$smarty.block.parent}
{/block}
{block name='frontend_detail_configurator_variant_group'}
    {$numberGroup = $numberGroup + 1}
    {$smarty.block.parent}
{/block}
{block name='frontend_detail_configurator_variant_group_option'}
    {if !($tlsVariantExtends.hideNotValid && $option.attributes.tlsVariantExtends && $option.attributes.tlsVariantExtends->get('invalidCombination'))}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='frontend_detail_configurator_variant_group_option_input'}
{$isDisabledInput = !$sArticle.notification && !$option.selectable && !$tlsVariantExtends.noSaleMessage && $onlyOneGroup}
{$isResetOther = !$sArticle.notification && !$option.selectable && !$tlsVariantExtends.noSaleMessage && !$onlyOneGroup}
{if !$tlsVariantExtends.hideNotValid && $option.attributes.tlsVariantExtends && $option.attributes.tlsVariantExtends->get('invalidCombination')}
    {if $onlyOneGroup}
        {$isDisabledInput = true}
        {$isResetOther = false}
    {else}
        {$isResetOther = true}
        {$isDisabledInput = false}
    {/if}
{/if}
    <input type="radio"
           class="option--input"
           id="group[{$option.groupID}][{$option.optionID}]"
           name="group[{$option.groupID}]"
           value="{$option.optionID}"
           title="{$option.optionname}"
           {if $theme.ajaxVariantSwitch}data-ajax-select-variants="true"{else}data-auto-submit="true"{/if}
           {if $isDisabledInput}disabled="disabled"{/if}
           {if $isResetOther}data-tls-reset-other="true"{/if}
           {if $option.selected && ($sArticle.notification || $option.selectable || $tlsVariantExtends.noSaleMessage)}checked="checked"{/if} />
{/block}

{block name='frontend_detail_configurator_variant_group_option_label'}
    {$isDisabledOptionLabel = !$sArticle.notification && !$option.selectable}
    {if $sArticle.notification && $tlsVariantExtends.opacityNotification}
        {$isOpacityOptionLabel = !$option.selectable}
    {/if}
    <label for="group[{$option.groupID}][{$option.optionID}]" class="option--label{if $isDisabledOptionLabel && !$isOpacityOptionLabel && $numberGroup != 1} is--disabled{elseif $isOpacityOptionLabel && !$option.media && $numberGroup !=1} is--opacity{/if}">

        {if $option.media}
            {$media = $option.media}

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
        {else}
            {block name='frontend_detail_configurator_variant_group_option_label_text'}
                {$option.optionname}
            {/block}
        {/if}
    </label>
{/block}
