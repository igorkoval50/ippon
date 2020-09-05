{block name="frontend_listing_product_box_button_buy"}

    {block name="frontend_listing_product_box_button_detail_url"}
        {$detailUrl = {$sArticle.linkDetails} }
    {/block}

    {block name="frontend_listing_product_box_button_detail_title"}
        {$title = {$sArticle.articleName|escapeHtml} }
    {/block}

    {block name="frontend_listing_product_box_button_detail_label"}
        {s name="ListingButtonDetail" namespace="frontend/listing/box_article" assign="label"}Details{/s}
    {/block}

    {block name="frontend_listing_product_box_button_buy_url"}
        {$url = {url controller=checkout action=addArticle} }
    {/block}

    {block name="frontend_listing_product_box_button_buy_button"}
        <div class="buy-button--both-button-container">
            {block name="frontend_listing_product_box_button_detail_container"}
                <div class="both-button-button-container both-button-container--left-button">
                    {block name="frontend_listing_product_box_button_detail_anchor"}
                        <a href="{$detailUrl}" class="btn is--center is--large"
                           title="{$label} - {$title}">
                            {block name="frontend_listing_product_box_button_detail_text"}
                                <i class="icon--info"></i>
                            {/block}
                        </a>
                    {/block}
                </div>
            {/block}

            <div class="both-button-button-container both-button-container--right-button">
                {block name="frontend_listing_product_box_button_buy_form"}
                    <form name="sAddToBasket"
                          method="post"
                          action="{$url}"
                          class="buybox--form"
                          data-add-article="true"
                          data-eventName="submit"
                            {if $theme.offcanvasCart}
                                data-showModal="false"
                                data-addArticleUrl="{url controller=checkout action=ajaxAddArticleCart}"
                            {/if}>

                        {block name="frontend_listing_product_box_button_buy_button"}
                            <button class="btn is--primary is--icon-right is--center is--large"
                                    aria-label="{s namespace="frontend/listing/box_article" name="ListingBuyActionAddText"}{/s}">
                                {block name="frontend_listing_product_box_button_buy_button_text"}
                                    {s namespace="frontend/listing/box_article" name="ListingBuyActionAdd"}{/s}
                                    <i class="icon--basket"></i>
                                    <i class="icon--arrow-right"></i>
                                {/block}
                            </button>
                        {/block}

                        {block name="frontend_listing_product_box_button_buy_order_number"}
                            <input type="hidden" name="sAdd" value="{$sArticle.ordernumber}"/>
                        {/block}
                    </form>
                {/block}
            </div>
        </div>
    {/block}
{/block}
