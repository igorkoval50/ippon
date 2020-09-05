{namespace name="frontend/tls_quick_order/content"}

<input type="hidden" name="orderNumber[{$productIndex}]" value="{$product.ordernumber}">
<div class="row">
    <div class="col column--option is--align-left"><span class="col--title">{s name='headerOption'}Variante{/s}:</span>{if $suboption['name']}{$suboption['name']}{else}{$option['name']}{/if}</div>
    <div class="col column--price is--align-right"><span class="col--title">{s name='headerPrice'}Preis{/s}:</span>
        {block name='frontend_tls_quick_order_price'}
            {$product['prices']['0'].price|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
        {/block}
    </div>
    <div class="col column--instock is--align-right"><span class="col--title">{s name='headerInstock'}Bestand{/s}:</span>{$product.instock}</div>
    <div class="col column--nextstock is--align-center"><span class="col--title">{s name='headerNextstock'}Nächste Lieferung{/s}:</span>{if $product.lieferzeit1}{$product.lieferzeit1}{else}<span><i class="icon--minus3"></i></span>{/if}</div>
    <div class="col column--additionalstock is--align-center"><span class="col--title">{s name='headerAdditionalstock'}Weitere Lieferung{/s}:</span>{if $product.lieferzeit2}{$product.lieferzeit2}{else}<span><i class="icon--minus3"></i></span>{/if}</div>
    <div class="col column--quantity is--align-center">
        <span class="col--title">{s name='headerQuantity'}Anzahl{/s}:</span>
        {block name='frontend_tls_quick_order_quantity'}
            {if $product.maxpurchase}
                {$maxQuantity=$product.maxpurchase+1}
            {else}
                {$maxQuantity=100+1}
            {/if}
            {if $product.laststock && $product.instock < $product.maxpurchase}
                {$maxQuantity=$product.instock+1}
            {/if}

            {block name='frontend_dtls_quick_order_select'}
                <div class="select-field">
                    {if $product.instock > 0}
                        <select name="quantity[{$productIndex}]" class="quantity--select" data-price="{$product['prices']['0'].price}">
                            <option value="0">
                                0{if $product.packunit} {$product.packunit}{/if}</option>
                            {section name="i" start=$product.minpurchase loop=$maxQuantity step=$product.purchasesteps}
                                <option value="{$smarty.section.i.index}">{$smarty.section.i.index}{if $product.packunit} {$product.packunit}{/if}</option>
                            {/section}
                        </select>
                    {else}
                        <select name="quantity[{$productIndex}]" class="quantity--select"
                                disabled="disabled">
                            <option value="0">
                                0{if $product.packunit} {$product.packunit}{/if}</option>
                        </select>
                    {/if}
                </div>
            {/block}
        {/block}
    </div>
</div>
