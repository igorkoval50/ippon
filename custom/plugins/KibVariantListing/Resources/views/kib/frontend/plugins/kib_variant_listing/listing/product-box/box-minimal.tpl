{extends file="parent:frontend/listing/product-box/box-minimal.tpl"}

{block name="frontend_listing_box_article_content"}
    <div
        class="{if $configurationsCount >= $KibVariantListing.minVariants && $KibVariantListing.enableVariantsInListing}has--variants{else}has--no-variants{if $KibVariantListing.zoomCover} zoom--cover{/if}{/if}{if $KibVariantListing.slideOut && ($productBoxLayout == 'minimal' || $productBoxLayout == 'image')} variant--slideout{/if}">
        {$smarty.block.parent}

        {if $KibVariantListing.enableVariantsInListing && $KibVariantListing.slideOut && $configurationsCount >= $KibVariantListing.minVariants}
            <div class="slideout-more-icon icon--plus3"></div>
        {/if}
    </div>
{/block}

{* Product Variant *}
{block name="frontend_listing_box_article_picture"}
    {$smarty.block.parent}

    {if $KibVariantListing.enableVariantsInListing}
        {block name="frontend_listing_box_article_variant_info"}
            {if $KibVariantListing.viewDropdown}
                {include file='frontend/plugins/kib_variant_listing/listing/listing_variants_dropdown.tpl' numberOfVariants=$KibVariantListing.numberOfVariants configurationsCount=$configurationsCount KibConfigurator=$KibConfigurator}
            {else}
                {include file='frontend/plugins/kib_variant_listing/listing/listing_variants.tpl' numberOfVariants=$KibVariantListing.numberOfVariants configurationsCount=$configurationsCount KibConfigurator=$KibConfigurator}
            {/if}
        {/block}
    {/if}
{/block}
