<div class="kss-configurator" data-ordernumber-search-url="{url controller='KssSliderArticlesDetail' action='getOrderNumberByGroup'}?id={$sArticle.articleID}">
    {block name='frontend_listing_box_article_price_info'}
        <div class="product--price-info">

            {* Product price - Unit price *}
            {block name='frontend_listing_box_article_unit'}
                {include file="frontend/listing/product-box/product-price-unit.tpl"}
            {/block}

            {* Product price - Default and discount price *}
            {block name='frontend_listing_box_article_price'}
                <div class="product--price-outer">
                    {include file="frontend/listing/product-box/product-price.tpl"}
                </div>
            {/block}
        </div>
    {/block}
{$sArticle = $sArticle.detailsArticle}
{$configurator = $sArticle.sConfigurator}
        {$url = {url controller=checkout action=addArticle} }
        <form name="nfr"
              method="post"
              action="{$url}"
              class="buybox--form"
              data-add-kssarticle="true"
              data-eventName="submit"
                {if $theme.offcanvasCart}
            data-showModal="false"
            data-addArticleUrl="{url controller=checkout action=ajaxAddArticleCart}"
                {/if}>
            {foreach $configurator as $configuratorGroup}
                        <p class="variant--name">{$configuratorGroup.groupname}</p>
                        <select name="selectgroup[{$configuratorGroup.groupID}]" id="selectgroup[{$configuratorGroup.groupID}]">
                            {foreach $configuratorGroup.values as $option}
                                <option class="variant--option"
                                       class="option--input"
                                       id="group[{$option.groupID}][{$option.optionID}]"
                                       name="group[{$option.groupID}]"
                                       value="{$option.optionID}"
                                       title="{$option.optionname}"
                                       {if $theme.ajaxVariantSwitch}data-ajax-select-variants="true"{else}data-auto-submit="true"{/if}
                                        {if !$sArticle.notification && !$option.selectable}disabled="disabled"{/if}
                                        {if $option.selected && ($sArticle.notification || $option.selectable)}selected="selected"{/if} >
                                    {$option.optionname}
                                </option>
                            {/foreach}
                        </select>
                {/foreach}
                <input type="hidden" name="sAdd" value="{$sArticle.ordernumber}"/>
                <button class="kssbuybox--button block btn is--primary is--icon-right is--center is--large" aria-label="{s namespace="frontend/listing/box_article" name="ListingBuyActionAddText"}{/s}">
                        {s namespace="frontend/listing/box_article" name="ListingBuyActionAdd"}{/s}<i class="icon--basket"></i> <i class="icon--arrow-right"></i>
                </button>
        </form>
</div>