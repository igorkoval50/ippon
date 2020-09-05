{extends file="parent:frontend/listing/box_article.tpl"}

{block name="frontend_listing_box_article_includes"}
    {$configurationsCount = 0}
    {$KibConfigurator = null}
    {if $sArticle.attributes.kib_variant_listing && $sArticle.attributes.kib_variant_listing->get('kib_configurator')}
        {$KibConfigurator = $sArticle.attributes.kib_variant_listing->get('kib_configurator')}
        {$configuratorCounter = 0}

        {foreach $KibConfigurator.sConfigurator as $configurator}
            {if $configuratorCounter == $KibVariantListing.maxConfiguratorLevel}
                {break}
            {/if}

            {foreach $configurator.values as $value}
                {if $KibVariantListing.showInactive &&
                ($value.media || ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                $KibVariantListing.textVariants)}
                    {$configurationsCount = $configurationsCount + 1}
                {elseif $value.selectable &&
                ($value.media || ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                $KibVariantListing.textVariants)}
                    {$configurationsCount = $configurationsCount + 1}
                {/if}
            {/foreach}

            {$configuratorCounter = $configuratorCounter + 1}
        {/foreach}
    {/if}

    {if $productBoxLayout == 'minimal'}
        {include file="frontend/plugins/kib_variant_listing/listing/product-box/box-minimal.tpl"}
    {elseif $productBoxLayout == 'image'}
        {include file="frontend/plugins/kib_variant_listing/listing/product-box/box-big-image.tpl"}
    {elseif $productBoxLayout == 'slider'}
        {include file="frontend/plugins/kib_variant_listing/listing/product-box/box-product-slider.tpl"}
    {elseif $productBoxLayout == 'emotion'}
        {include file="frontend/plugins/kib_variant_listing/listing/product-box/box-emotion.tpl"}
    {elseif $productBoxLayout == 'list'}
        {include file="frontend/plugins/kib_variant_listing/listing/product-box/box-list.tpl"}
    {else}
        {block name="frontend_listing_box_article_includes_additional"}
            {include file="frontend/plugins/kib_variant_listing/listing/product-box/box-basic.tpl" productBoxLayout="basic"}
        {/block}
    {/if}
{/block}
