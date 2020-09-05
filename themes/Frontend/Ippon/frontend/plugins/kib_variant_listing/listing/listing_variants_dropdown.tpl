{extends file="parent:frontend/plugins/kib_variant_listing/listing/listing_variants_dropdown.tpl"}




{block name='frontend_listing_kib_variant_listing_dropdown_option'}
    <li class="kib-product--variant product--variant--dropdown-option">
        <a href="{url controller='detail' action='index'
        sArticle=$sArticle.articleID number=$value.attributes.kib_configurator_ordernumbers->get('option_ordernumber')}"
           title="{$sArticle.articleName|escape} {$value.optionname|escape}"
           class="product--variant--imagebox{if $value.attributes.kib_configurator_ordernumbers->get('option_ordernumber') == $sArticle.ordernumber && $value.media.id == $sArticle.image.id} is--main-cover{/if}"
           data-listing-cover="{if  $productBoxLayout == 'image' || $productBoxLayout == 'emotion'}{$value.media.thumbnails[1].sourceSet}{else}{$value.media.thumbnails[0].sourceSet}{/if}">
            {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
                <img class="lazyLoad" data-srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
                    alt="{$value.optionname}">
                {$value.optionname}
            {elseif $KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback}
                {block name='frontend_listing_kib_variant_listing_option_label_text'}
                    {$value.optionname}
                {/block}
            {elseif $KibConfigurator.sConfiguratorSettings.type != 2 && $KibVariantListing.textVariants}
                {block name='frontend_listing_kib_variant_listing_option_label_text'}
                    {$value.optionname}
                {/block}
            {/if}
        </a>
    </li>
{/block}

{block name='frontend_listing_kib_variant_listing_dropdown_option_disabled'}
    <li class="kib-product--variant product--variant--dropdown-option">
        <div class="product--variant--imagebox is--disabled">
            {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
                <img class="lazyLoad" data-srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
                    alt="{$value.optionname}">
                {$value.optionname}
            {elseif $KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback}
                {block name='frontend_listing_kib_variant_listing_option_label_text'}
                    {$value.optionname}
                {/block}
            {elseif $KibConfigurator.sConfiguratorSettings.type != 2 && $KibVariantListing.textVariants}
                {block name='frontend_listing_kib_variant_listing_option_label_text'}
                    {$value.optionname}
                {/block}
            {/if}
        </div>
    </li>
{/block}
