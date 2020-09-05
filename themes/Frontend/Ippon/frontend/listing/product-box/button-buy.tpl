{extends file="parent:frontend/listing/product-box/button-buy.tpl"}

{block name="frontend_listing_product_box_button_buy_form"}
    {if $sArticle.anfrageartikel}
        <div class="product--detail-btn">
            {assign var="sInquiry" value="{url controller="anfrage-formular"}?sInquiry=detail&sOrdernumber={$sArticle.ordernumber}"}
            {block name='frontend_listing_product_box_button_buy_button_inquiry'}
                <a href="{$sInquiry}" class="inquiry--btn block btn is--secondary is--ghost is--icon-right is--center is--large" title="{"{s name='DetailLinkContact' namespace="frontend/detail/actions"}{/s}"|escape}">
                    <span class="storelocator--text">{s name="DetailLinkContact" namespace="frontend/detail/actions"}{/s}</span>
                    <i class="icon--arrow-right"></i>
                </a>
            {/block}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}