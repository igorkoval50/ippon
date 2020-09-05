{extends file="parent:frontend/detail/buy.tpl"}

{* Quantity selection *}
{block name='frontend_detail_buy_quantity'}
    <div class="price-count--block">
        <div class="buybox--quantity block">
            {$maxQuantity=$sArticle.maxpurchase+1}
            {$sQuantityValue = $sArticle.minpurchase}

            {if $sArticle.laststock && $sArticle.instock < $sArticle.maxpurchase}
                {$maxQuantity=$sArticle.instock+1}
            {/if}
            <div class="product--quantity-block" data-quantitySelect="true">
                <span class="quantity--minus">
                    <i class="icon--minus3"></i>
                </span>
                <input id="sQuantity"
                       data-step="{$sArticle.purchasesteps}"
                       data-start="{$sArticle.minpurchase}"
                       data-finish="{$maxQuantity}"
                       name="sQuantity"
                       autocomplete="off"
                       class="quantity--select {if $sArticle.kss_product_quantity_input}is--disabled{/if}"
                       value="{$sQuantityValue}"
                       type="number">
                <span class="quantity--plus">
                    <i class="icon--plus3"></i>
                </span>
            </div>
        </div>
    </div>
{/block}

{* @Dupp: Show help-message if no configurator selection is set *}
{block name="frontend_detail_buy_button_container_outer"}
    {if $sArticle.sConfigurator && !$activeConfiguratorSelection}
        {include file="frontend/_includes/messages.tpl" type="warning" content="{s name='DetailConfiguratorWarning' namespace='frontend/detail/buy'}Es ist aktuell noch keine Variante gewählt, bitte wählen Sie die gewünschte Varianten aus.{/s}"}
    {/if}

    {$smarty.block.parent}
{/block}

{block name="frontend_detail_buy_button_container"}
    {if $sArticle.anfrageartikel}
        <div class="product--storelocator is--only block-group">
            {block name='frontend_detail_buy_button_container_inquiry'}
                <a href="{$sInquiry}" class="1337 inquiry--btn block btn is--secondary is--ghost is--icon-right is--center is--large" title="{"{s name='DetailLinkContact' namespace="frontend/detail/actions"}{/s}"|escape}">
                    <span class="storelocator--text">{s name="DetailLinkContact" namespace="frontend/detail/actions"}{/s}</span>
                    <i class="icon--arrow-right"></i>
                </a>
            {/block}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="frontend_detail_buy_button_container_outer"}

    {if $sArticle.anfrageartikel}
    <div class="product--storelocator is--only block-group">
        {block name='frontend_detail_buy_button_container_inquiry'}
            <a href="{$sInquiry}" class="1337 inquiry--btn block btn is--secondary is--ghost is--icon-right is--center is--large" title="{"{s name='DetailLinkContact' namespace="frontend/detail/actions"}{/s}"|escape}">
                <span class="storelocator--text">{s name="DetailLinkContact" namespace="frontend/detail/actions"}{/s}</span>
                <i class="icon--arrow-right"></i>
            </a>
        {/block}
    </div>
    {else}

    {if (!isset($sArticle.active) || $sArticle.active)}
        {if $sArticle.isAvailable}
            {block name="frontend_detail_buy_button_container"}
                <div class="buybox--button-container block-group{if $NotifyHideBasket && $sArticle.notification && $sArticle.instock < $sArticle.minpurchase} is--hidden{/if}">

                    {* Quantity selection *}
                    {block name='frontend_detail_buy_quantity'}
                        <div class="buybox--quantity block">
                            {$maxQuantity=$sArticle.maxpurchase+1}
                            {if $sArticle.laststock && $sArticle.instock < $sArticle.maxpurchase}
                                {$maxQuantity=$sArticle.instock+1}
                            {/if}

                            {block name='frontend_detail_buy_quantity_select'}
                                <div class="select-field">
                                    <select id="sQuantity" name="sQuantity" class="quantity--select">
                                        {section name="i" start=$sArticle.minpurchase loop=$maxQuantity step=$sArticle.purchasesteps}
                                            <option value="{$smarty.section.i.index}">{$smarty.section.i.index}{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
                                        {/section}
                                    </select>
                                </div>
                            {/block}
                        </div>
                    {/block}

                    {* "Buy now" button *}
                    {block name="frontend_detail_buy_button"}
                        {if $sArticle.sConfigurator && !$activeConfiguratorSelection}
                            <button class="buybox--button block btn is--disabled is--icon-right is--large" disabled="disabled" aria-disabled="true" name="{s name="DetailBuyActionAddName"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
                                {s name="DetailBuyActionAdd"}{/s}
                            </button>
                        {else}
                            <button class="buybox--button block btn is--primary is--icon-right is--center is--large" name="{s name="DetailBuyActionAddName"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
                                {s name="DetailBuyActionAdd"}{/s}
                            </button>
                        {/if}
                    {/block}

                    {block name='frontend_detail_actions_notepad'}
                        <form action="{url controller='note' action='add' ordernumber=$sArticle.ordernumber}" method="post" class="action--form">
                            {s name="DetailLinkNotepad" assign="snippetDetailLinkNotepad"}{/s}
                            <button type="submit"
                                    class="action--link link--notepad"
                                    title="{$snippetDetailLinkNotepad|escape}"
                                    data-ajaxUrl="{url controller='note' action='ajaxAdd' ordernumber=$sArticle.ordernumber}"
                                    data-text="{s name="DetailNotepadMarked"}{/s}">
                                <i class="icon--heart"></i>
                            </button>
                        </form>
                    {/block}
                </div>
            {/block}
        {/if}
    {/if}

    {/if}

{/block}