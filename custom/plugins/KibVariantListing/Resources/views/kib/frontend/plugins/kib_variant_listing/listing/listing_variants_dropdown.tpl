{block name='frontend_listing_kib_variant_listing'}
    <div class="product--variants--info--wrapper">
        {block name='frontend_listing_kib_variant_listing_dropdown'}
            <div class="product--variants--info"
                 data-cover-delay="{$KibVariantListing.variantCoverDelay}">
                {if $configurationsCount >= $KibVariantListing.minVariants && $KibConfigurator != null}
                    <div class="product--variant--dropdown-trigger" data-drop-down-menu="true"
                         data-preventDefault="false" data-blockedElements=".product--variant--imagebox">
                        <div
                            class="select-field">{s name="KibVariantListingDropDownItems"}verfügbare Varianten...{/s}</div>
                        <ul class="product--variant--dropdown">
                            {$counter = 0}
                            {$configuratorCounter = 0}

                            {foreach $KibConfigurator.sConfigurator as $configurator}
                                {block name='frontend_listing_kib_variant_listing_dropdown_configurators'}
                                    {if $counter == $numberOfVariants || $configuratorCounter == $KibVariantListing.maxConfiguratorLevel}
                                        {break}
                                    {/if}

                                    {foreach $configurator.values as $value}
                                        {block name='frontend_listing_kib_variant_listing_dropdown_options'}
                                            {if ($value.selectable || $KibVariantListing.showInactive) &&
                                            ($value.media || ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                                            $KibVariantListing.textVariants)}
                                                {$counter = $counter + 1}

                                                {block name='frontend_listing_kib_variant_listing_dropdown_option_more'}
                                                    {if $counter == $numberOfVariants && $configurationsCount > $numberOfVariants}
                                                        <li
                                                            value="{url controller='detail' action='index' sArticle=$sArticle.articleID}"
                                                            title="{$sArticle.articleName|escape}"
                                                            class="kib-product--variant product--variant--dropdown-option">
                                                            <a href="{url controller='detail' action='index' sArticle=$sArticle.articleID}"
                                                               title="{$sArticle.articleName|escape}"
                                                               class="product--variant--imagebox product--variant--more">
                                                                {s name="KibVariantListingMoreItems"}mehr...{/s}
                                                            </a>
                                                        </li>
                                                        {break}
                                                    {/if}
                                                {/block}

                                                {if $value.selectable &&
                                                ($value.media.thumbnails[0].sourceSet ||
                                                ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                                                ($KibConfigurator.sConfiguratorSettings.type != 2 && $KibVariantListing.textVariants))}
                                                    {block name='frontend_listing_kib_variant_listing_dropdown_option'}
                                                        <div class="kib-product--variant-wrapper"
                                                             {if $value.attributes.kib_configurator_ordernumbers->get('option_ordernumber') != $sArticle.ordernumber}data-ordernumber="{$value.attributes.kib_configurator_ordernumbers->get('option_ordernumber')}"{/if}>
                                                            <li class="kib-product--variant product--variant--dropdown-option">
                                                                <a href="{url controller='detail' action='index'
                                                                sArticle=$sArticle.articleID number=$value.attributes.kib_configurator_ordernumbers->get('option_ordernumber')}"
                                                                   title="{$sArticle.articleName|escape} {$value.optionname|escape}"
                                                                   class="product--variant--imagebox{if $value.attributes.kib_configurator_ordernumbers->get('option_ordernumber') == $sArticle.ordernumber && $value.media.id == $sArticle.image.id} is--main-cover{/if}"
                                                                   data-listing-cover="{if  $productBoxLayout == 'image' || $productBoxLayout == 'emotion'}{$value.media.thumbnails[1].sourceSet}{else}{$value.media.thumbnails[0].sourceSet}{/if}">
                                                                    {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
                                                                        <img
                                                                            srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
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
                                                        </div>
                                                    {/block}
                                                {elseif $value.media.thumbnails[0].sourceSet ||
                                                ($KibConfigurator.sConfiguratorSettings.type == 2 && $KibVariantListing.titleFallback) ||
                                                ($KibConfigurator.sConfiguratorSettings.type != 2 && $KibVariantListing.textVariants)}
                                                    {block name='frontend_listing_kib_variant_listing_dropdown_option_disabled'}
                                                        <li class="kib-product--variant product--variant--dropdown-option">
                                                            <div class="product--variant--imagebox is--disabled">
                                                                {if $value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}
                                                                    <img
                                                                        srcset="{$value.media.thumbnails[$KibVariantListing.thumbnailRef].sourceSet}"
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
                                                {/if}
                                            {/if}
                                        {/block}
                                    {/foreach}

                                    {$configuratorCounter = $configuratorCounter + 1}
                                {/block}
                            {/foreach}
                        </ul>
                    </div>
                {/if}
            </div>
        {/block}
    </div>
{/block}
