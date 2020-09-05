{extends file="parent:frontend/plugins/kib_variant_listing/listing/listing_variants.tpl"}

{block name='frontend_listing_kib_variant_listing_option'}
    <a href="{url controller='detail' action='index'
    sArticle=$sArticle.articleID number=$value.attributes.kib_configurator_ordernumbers->get('option_ordernumber')}"
       title="{$sArticle.articleName|escape} {$value.optionname|escape}"
       class="kib-product--variant product--variant--imagebox image-slider--item{if $value.attributes.kib_configurator_ordernumbers->get('option_ordernumber') == $sArticle.ordernumber && $value.media.id == $sArticle.image.id} is--main-cover{/if}"
       data-listing-cover="{if  $productBoxLayout == 'image' || $productBoxLayout == 'emotion'}{$value.media.thumbnails[1].sourceSet}{else}{$value.media.thumbnails[0].sourceSet}{/if}">
        {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
            <img class="lazyLoad" data-srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
                alt="{$value.optionname}">
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
{/block}

{block name='frontend_listing_kib_variant_listing_option_disabled'}
    <span class="kib-product--variant image-slider--item is--disabled">
        {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
            <img class="lazyLoad" data-srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
            alt="{$value.optionname}">
        {elseif $KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback}
            {block name='frontend_listing_kib_variant_listing_option_label_text'}
                {$value.optionname}
            {/block}
        {elseif $KibConfigurator.sConfiguratorSettings.type != 2 && $KibVariantListing.textVariants}
            {block name='frontend_listing_kib_variant_listing_option_label_text'}
                {$value.optionname}
            {/block}
        {/if}
    </span>
{/block}
