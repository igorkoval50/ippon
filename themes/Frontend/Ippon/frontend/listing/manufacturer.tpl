{extends file="parent:frontend/listing/manufacturer.tpl"}

{* Vendor content e.g. description and logo *}
{block name="frontend_listing_list_filter_supplier_content"}
    <div class="panel--body is--wide">

        {if $manufacturer->getCoverFile()}
            <div class="vendor--image-wrapper">
                <img class="vendor--image lazyLoad" data-src="{$manufacturer->getCoverFile()}" alt="{$manufacturer->getName()|escape}">
            </div>
        {/if}

        {if $manufacturer->getDescription()}
            <div class="vendor--text">
                {$manufacturer->getDescription()}
            </div>
        {/if}
    </div>
{/block}
