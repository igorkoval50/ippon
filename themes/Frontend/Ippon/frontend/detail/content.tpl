{extends file="parent:frontend/detail/content.tpl"}

{* @Dupp - {include file="frontend/detail/content/header.tpl"} moved
   into product--detail-upper because of Ajax Variants *}

{block name='frontend_index_content_inner'}
    <div class="content product--details" itemscope
         itemtype="http://schema.org/Product"{if !{config name=disableArticleNavigation}} data-product-navigation="{url module="widgets" controller="listing" action="productNavigation"}" data-category-id="{$sArticle.categoryID}" data-main-ordernumber="{$sArticle.mainVariantNumber}"{/if}
         data-ajax-wishlist="true"
         data-compare-ajax="true"{if $theme.ajaxVariantSwitch} data-ajax-variants-container="true"{/if}>

        {* The configurator selection is checked at this early point
           to use it in different included files in the detail template. *}
        {block name='frontend_detail_index_configurator_settings'}

            {* Variable for tracking active user variant selection *}
            {$activeConfiguratorSelection = true}

            {if $sArticle.sConfigurator && ($sArticle.sConfiguratorSettings.type == 1 || $sArticle.sConfiguratorSettings.type == 2)}
                {* If user has no selection in this group set it to false *}
                {foreach $sArticle.sConfigurator as $configuratorGroup}
                    {if !$configuratorGroup.selected_value}
                        {$activeConfiguratorSelection = false}
                    {/if}
                {/foreach}
            {/if}
        {/block}

        {* @Dupp - Product header *}
        {*{include file="frontend/detail/content/header.tpl"}*}

        <div class="product--detail-upper block-group">

            <div class="slider-container">
                <div class="mobile">
                    {* Product image mobile *}
                    {block name='frontend_detail_index_image_container'}
                        <div class="product--image-container image-slider{if $sArticle.image && {config name=sUSEZOOMPLUS}} product--image-zoom{/if}"
                                {if $sArticle.image}
                            data-image-slider="true"
                            data-image-gallery="true"
                            data-maxZoom="{$theme.lightboxZoomFactor}"
                            data-thumbnails=".image--thumbnails"
                                {/if}>
                            {include file="frontend/detail/image.tpl"}
                        </div>
                    {/block}
                </div>
                <div class="desktop">
                    {* Product image desctop *}
                    {block name='frontend_detail_index_image_container'}
                    <div class="custom-slider" data-tls-custom-images="true">
                        {include file="frontend/detail/custom-slider.tpl"}
                    </div>
                    {/block}
                </div>
            </div>

            <div class="overflow">

                {* ... move Product header *}
                {include file="frontend/detail/content/header.tpl"}

                {* "Buy now" box container *}
                {include file="frontend/detail/content/buy_container.tpl"}

                {block name="frontend_detail_index_detail"}
                    {* Tab navigation *}
                    {block name="frontend_detail_index_tabs"}
                        {include file="frontend/detail/tabs.tpl"}
                    {/block}
                {/block}

                <div class="custom-collapse">
                    <div class="custom-collapse-quick-order-headline">
                        {include file="frontend/tls_quick_order/tabs.tpl"}
                    </div>
                    <div class="custom-collapse-quick-order-container">
                        {include file="frontend/tls_quick_order/content.tpl"}
                    </div>
                </div>

                <div class="custom-collapse has--border">
                    <div class="custom-collapse-headline">
                        <span>{s name="CustomCollapseTitle"}Lieferung und Ruckgabe{/s}</span>
                    </div>
                    <div class="custom-collapse-container">
                        {s name="CustomCollapseContent"}Custom Collapse Content{/s}
                    </div>
                </div>

                {* Product bundle hook point *}
                <div class="custom-collapse bundle has--border">
                    <div class="custom-collapse-headline">
                        <span>{s name="CustomCollapseBundleTitle"}Bundle{/s}</span>
                    </div>
                    <div class="custom-collapse-container">
                        {* Product bundle hook point *}
                        {block name="frontend_detail_index_bundle"}{/block}
                    </div>
                </div>

            </div>
        </div>

        {* Crossselling tab panel *}
        {block name="frontend_detail_index_tabs_cross_selling"}
            {$showAlsoViewed = {config name=similarViewedShow}}
            {$showAlsoBought = {config name=alsoBoughtShow}}
            <div class="tab-menu--cross-selling"{if $sArticle.relatedProductStreams} data-scrollable="true"{/if}>
                {* Tab navigation *}
                {include file="frontend/detail/content/tab_navigation.tpl"}

                {* Tab content container *}
                {include file="frontend/detail/content/tab_container.tpl"}
            </div>
        {/block}

        {* Crossselling tab panel *}
        {block name="frontend_detail_index_tabs_cross_selling_bottom"}
            {$showAlsoViewed = {config name=similarViewedShow}}
            {$showAlsoBought = {config name=alsoBoughtShow}}
            <div class="tab-menu--cross-selling"{if $sArticle.relatedProductStreams} data-scrollable="true"{/if}>
                {* Tab navigation *}
                {*{include file="frontend/detail/content/tab_navigation1.tpl"}*}

                {* Tab content container *}
                {*{include file="frontend/detail/content/tab_container1.tpl"}*}
            </div>
        {/block}

    </div>
{/block}
