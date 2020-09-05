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
        <div class="kss-configurator" data-ordernumber-search-url="{url controller='KssSliderArticlesDetail' action='getOrderNumberByGroup'}?id={$sArticle.articleID}">
                    {if $sArticle.attributes.kss_variant_listing && $sArticle.attributes.kss_variant_listing->get('kss_configurator')}
                        {$configurator = $sArticle.attributes.kss_variant_listing->get('kss_configurator')}
                        {foreach $configurator.sConfigurator as $configuratorGroup}
                            <p class="variant--name">{$configuratorGroup.groupname}</p>
                            <select name="selectgroup[{$configuratorGroup.groupID}]" id="selectgroup[{$configuratorGroup.groupID}]">
                                <option class="variant--option"
                                        class="option--input"
                                        value=""
                                        title=""
                                        selected="selected" >
                                </option>
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
                    {/if}
                        <input type="hidden" name="sAdd" value="{$sArticle.ordernumber}"/>

                        <button class="kssbuybox--button block btn is--primary is--icon-right is--center is--large " {if $configurator.disable_buy}disabled="disabled"{/if} aria-label="{s namespace="frontend/listing/box_article" name="ListingBuyActionAddText"}{/s}">
                            {block name="frontend_listing_product_box_button_buy_button_text"}
                                {s namespace="frontend/listing/box_article" name="ListingBuyActionAdd"}{/s}<i class="icon--basket"></i> <i class="icon--arrow-right"></i>
                            {/block}
                        </button>
        </div>
        </form>
    {/if}
{/block}