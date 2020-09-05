{namespace name="frontend/tls_quick_order/content"}

{$productIndex = 0}
{foreach from=$tlsQuickOrder['variants']  item=option name="allvariants"}
    {if $option['options']}
    <div class="tlsquickorder--group">
        <div class="tlsquickorder-collapse--title">{if $option['thumbnails']}<img class="lazyLoad" data-src="{$option['thumbnails']}" alt="{$option['name']}">{/if} {$option['groupName']}: {$option['name']}</div>
        <div class="tlsquickorder-collapse--content product--table">
            <div class="tlsquickorder--header">
                <div class="column--option column--title is--align-left">
                    {s name='headerOption'}Variante{/s}
                </div>
                <div class="column--price column--title is--align-right">
                    {s name='headerPrice'}Preis{/s}
                </div>
                <div class="column--instock column--title is--align-right">
                    {s name='headerInstock'}Bestand{/s}
                </div>
                <div class="column--nextstock column--title is--align-center">
                    {s name='headerNextstock'}Nächste Lieferung{/s}
                </div>
                <div class="column--additionalstock column--title is--align-center">
                    {s name='headerAdditionalstock'}Weitere Lieferung{/s}
                </div>
                <div class="column--quantity column--title is--align-center">
                    {s name='headerQuantity'}Anzahl{/s}
                </div>
            </div>
            {foreach $option['options'] as $suboption}
                {if $suboption['product']}
                    {$productIndex = $productIndex + 1}
                    {include file="frontend/tls_quick_order/item.tpl" product=$suboption['product'] productIndex=$productIndex}
                {/if}
            {/foreach}
        </div>
    </div>
    {else}
        {if $option['product']}
            {assign var="counter" value=$smarty.foreach.allvariants.iteration}
            {if $counter == 1}
                <div class="tlsquickorder--group">
                    <div class="product--table">
                        <div class="tlsquickorder--header">
                            <div class="column--option column--title is--align-left">
                                {s name='headerOption'}Variante{/s}
                            </div>
                            <div class="column--price column--title is--align-right">
                                {s name='headerPrice'}Preis{/s}
                            </div>
                            <div class="column--instock column--title is--align-right">
                                {s name='headerInstock'}Bestand{/s}
                            </div>
                            <div class="column--nextstock column--title is--align-center">
                                {s name='headerNextstock'}Nächste Lieferung{/s}
                            </div>
                            <div class="column--additionalstock column--title is--align-center">
                                {s name='headerAdditionalstock'}Weitere Lieferung{/s}
                            </div>
                            <div class="column--quantity column--title is--align-center">
                                {s name='headerQuantity'}Anzahl{/s}
                            </div>
                        </div>
            {/if}
                {$productIndex = $productIndex + 1}
                {include file="frontend/tls_quick_order/item.tpl" product=$option['product'] productIndex=$productIndex}
            {if $smarty.foreach.allvariants.last}
                    </div>
                </div>
            {/if}
        {/if}
    {/if}
{/foreach}
