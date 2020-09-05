{block name='frontend_listing_kib_variant_listing'}
    <div class="image-slider product--variants--info--wrapper"
         data-kib-variant-slider="{if $configurationsCount >= $KibVariantListing.minVariants}true{else}false{/if}">
        {block name='frontend_listing_kib_variant_listing_slider'}
            <div class="image-slider--container product--variants--info"
                 data-cover-delay="{$KibVariantListing.variantCoverDelay}"
                 data-slide-variants="{$KibVariantListing.slideVariants}">
                {if $configurationsCount >= $KibVariantListing.minVariants && $KibConfigurator != null}
                    <div class="image-slider--slide">
                        {$counter = 0}
                        {$configuratorCounter = 0}

                        {foreach $KibConfigurator.sConfigurator as $configurator}
                            {block name='frontend_listing_kib_variant_listing_configurators'}
                                {if $counter == $numberOfVariants || $configuratorCounter == $KibVariantListing.maxConfiguratorLevel}
                                    {break}
                                {/if}

                                {foreach $configurator.values as $value}
                                    {block name='frontend_listing_kib_variant_listing_options'}
                                        {if ($value.selectable || $KibVariantListing.showInactive) &&
                                        ($value.media || ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                                        $KibVariantListing.textVariants)}
                                            {$counter = $counter + 1}

                                            {block name='frontend_listing_kib_variant_listing_option_more'}
                                                {if $counter == $numberOfVariants && $configurationsCount > $numberOfVariants}
                                                    <a href="{url controller='detail' action='index' sArticle=$sArticle.articleID}"
                                                       title="{$sArticle.articleName|escape}"
                                                       class="kib-product--variant product--variant--more image-slider--item">
                                                        <i class="product--variant--more--icon btn icon--plus3"></i>
                                                    </a>
                                                    {break}
                                                {/if}
                                            {/block}

                                            {if $value.selectable &&
                                            ($value.media.thumbnails[0].sourceSet ||
                                            ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                                            ($KibConfigurator.sConfiguratorSettings.type != 2 && $KibVariantListing.textVariants))}
                                                {block name='frontend_listing_kib_variant_listing_option'}
                                                    <div class="kib-product--variant-wrapper"
                                                         {if $value.attributes.kib_configurator_ordernumbers->get('option_ordernumber') != $sArticle.ordernumber}data-ordernumber="{$value.attributes.kib_configurator_ordernumbers->get('option_ordernumber')}"{/if}>
                                                        <a href="{url controller='detail' action='index'
                                                        sArticle=$sArticle.articleID number=$value.attributes.kib_configurator_ordernumbers->get('option_ordernumber')}"
                                                           title="{$sArticle.articleName|escape} {$value.optionname|escape}"
                                                           class="kib-product--variant product--variant--imagebox image-slider--item{if $value.attributes.kib_configurator_ordernumbers->get('option_ordernumber') == $sArticle.ordernumber && $value.media.id == $sArticle.image.id} is--main-cover{/if}"
                                                           data-listing-cover="{if  $productBoxLayout == 'image' || $productBoxLayout == 'emotion'}{$value.media.thumbnails[1].sourceSet}{else}{$value.media.thumbnails[0].sourceSet}{/if}">
                                                            {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
                                                                <img
                                                                    srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
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
                                                    </div>
                                                {/block}
                                            {elseif $value.media.thumbnails[0].sourceSet ||
                                            ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                                            ($KibConfigurator.sConfiguratorSettings.type != 2 && $KibVariantListing.textVariants)}
                                                {block name='frontend_listing_kib_variant_listing_option_disabled'}
                                                    <span class="kib-product--variant image-slider--item is--disabled">
                                {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
                                    <img srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
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
                                            {/if}
                                        {/if}
                                    {/block}
                                {/foreach}

                                {$configuratorCounter = $configuratorCounter + 1}
                            {/block}
                        {/foreach}
                    </div>
                {/if}
            </div>
        {/block}
    </div>
{/block}
