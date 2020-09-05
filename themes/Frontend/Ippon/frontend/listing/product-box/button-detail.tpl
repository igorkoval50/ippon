{extends file="parent:frontend/listing/product-box/button-detail.tpl"}

{block name="frontend_listing_product_box_button_detail_anchor"}
    <a href="{$url}" class="buybox--button block btn is--primary is--icon-right is--center is--large" title="{$label} - {$title}">
        {block name="frontend_listing_product_box_button_detail_text"}
            {$label} <i class="icon--arrow-right"></i>
        {/block}
    </a>
{/block}