{extends file="parent:frontend/plugins/pixelvariantlistingswf/detail/pixelvariantlisting_batchdetail.tpl"}



{block name="frontend_index_header_css_screen" append}

    {*
    *
    * @Dupp: if $theme.showvariantlisting
    *
    *
    *}
    {if $theme.showvariantlisting}
    <link type="text/css" media="screen, projection" rel="stylesheet"
          href="{link file='frontend/plugins/pixelvariantlistingswf/_resources/styles/pixelvariantlisting_batchdetail.css'}"/>
    {/if}
{/block}
{block name="frontend_index_header_javascript_jquery_lib" append}

    {*
    *
    * @Dupp: if $theme.showvariantlisting
    *
    *
    *}
    {if $theme.showvariantlisting}
    <script type="text/javascript">


        {if $theme.asyncJavascriptLoading}
        document.asyncReady(function () {
            {else}
            $(document).ready(function () {
                {/if}

                var artofBatching = 'batchdetail';

                controller.send_pixvariantform = '{url controller=Pixelvariantlistingswf action=SendForm}';


                //Extends jQuery's namespace
                $.pixvariantlistingform = {};

                //Default settings
                $.pixvariantlistingform.options = {
                    viewport: '',
                    getviewport: '',
                    savetext: '{s name="CheckoutPixFormSave" namespace="frontend/checkout/cart_footer_left"}Daten wurden gespeichert{/s}',
                    pixvariantlistingformLoader: '.ajax_pixvariantlistingform .ajax_loader',
                    pixvariantlistingformResult: '.ajax_pixvariantlistingform_result',
                    pixvariantlistingformErrors: '.ajax_pixvariantlistingform_errors',
                    ordernumber: '{se name="DetailDataId" namespace="frontend/detail/data"}{/se}'
                };


                $.pixvariantlistingform.getForm = function () {


                    $.ajax({

                        'dataType': 'json',
                        'url': $.pixvariantlistingform.options.getviewport,
                        'success': function (result) {


                            $.each(result.data, function (key, value) {


                            });

                        }
                    });
                }


                $.pixvariantlistingform.sendForm = function (form) {

                    $.loadingIndicator.open({
                        'openOverlay': true
                    });

                    $('.js--overlay').addClass('is--open is--closable');

                    var str = encodeURIComponent($(form).serialize()).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+').replace(/~/g, '%7E');


                    $.ajax({
                        data: {

                            'data': str,
                        },
                        type: "POST",
                        dataType: 'json',
                        url: $.pixvariantlistingform.options.viewport,
                        success: function (responseJson) {


                            $.loadingIndicator.close();


                            if (responseJson.success == true) {


                                if (responseJson.stock) {

                                    var setErrors = "";


                                    $.each(responseJson.stock, function (key, value) {

                                        if (value.stockinfo)
                                            setErrors += '<div class="error"><ul><li><b>' + $.pixvariantlistingform.options.ordernumber + ' ' + value.sAdd + '</b><br>' + value.stockinfo + '</li></ul></div>';


                                    });


                                    $($.pixvariantlistingform.options.pixvariantlistingformErrors).empty().html(setErrors);
                                    $($.pixvariantlistingform.options.pixvariantlistingformErrors).fadeIn();


                                    window.setTimeout(function () {
                                        $($.pixvariantlistingform.options.pixvariantlistingformErrors).fadeOut();
                                    }, 5000);
                                }


                                $($.pixvariantlistingform.options.pixvariantlistingformResult).empty().html('<div class="alert is--success is--rounded"><div class="alert--icon"><i class="icon--element icon--check"></i></div><div class="alert--content">' + $.pixvariantlistingform.options.savetext + '</div></div>');
                                $($.pixvariantlistingform.options.pixvariantlistingformResult).fadeIn();

                                window.setTimeout(function () {
                                    $($.pixvariantlistingform.options.pixvariantlistingformResult).fadeOut();
                                }, 5000);

                            } else {
                                $($.pixvariantlistingform.options.pixvariantlistingformResult).empty().html();
                            }

                            cartAfterRefresh();

                            try{
                                $.loadingIndicator.close({
                                    'openOverlay': true
                                });
                            } catch (e) {


                            }

                            $('.js--overlay').removeClass('is--open is--closable');

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                            console.log(textStatus);
                        }
                    });
                }


                $.fn.extend({
                    VariantCalc: function (options) {

                        return this.each(function () {
                            var VariantContainer = $(this);


                            $(this).find("select").bind('change', function () {
                                berechnen();
                            });


                            function berechnen() {

                                var Total = 0;
                                var Currency = '{$Mcurrency.currency}';


                                $(VariantContainer).find("select").each(function (index, value) {

                                    var Anzahl = parseInt($("#" + value.id + " option:selected").val());


                                    if (Anzahl > 0 && !isNaN(Anzahl)) {
                                        value.value = Anzahl;

                                        var Variantenpreis = parseFloat($(value).attr('brutto').replace(",", ".")) * Anzahl;
                                        Total += Variantenpreis;
                                        Variantenpreis = Variantenpreis > 0 ? Variantenpreis + " " + Currency : "&nbsp;";
                                        $(value).parent().parent().next().find('span').html(Variantenpreis);

                                    } else {
                                        $(value).parent().parent().next().find('span').html("0,00 " + Currency);

                                    }


                                });

                                $("#gesamt").html(Total.toFixed(2) + " " + Currency);


                            }
                        });
                    }
                });

                $("#pixvariantlistingform .artbox_thumb").click(function () {

                    return false;
                });

                var variantdropdown = {$pixelvariantconfig.VARIANTFORM};
                variantdropdown = variantdropdown > 0 ? true : false;

                if (variantdropdown == true) {

                    $('.configurator--variant').hide();
                    $('.product--details .configurator--form, .product--details .buybox--form').hide();
                }
                $.pixvariantlistingform.options.viewport = controller.send_pixvariantform;
                $.pixvariantlistingform.options.getviewport = controller.get_pixvariantform;
                $.pixvariantlistingform.getForm();

                $('#pixvariantlistingform').bind('submit', function (event) {
                    event.preventDefault();

                    $.pixvariantlistingform.sendForm(this);

                });

                $('.pixinfotip').bind('click', function (event) {


                    $(this).parents('.pixmultiheader').next("p.pixmultidesc").slideToggle("fast");

                });

                $('.pixbasketheader').bind('click', function (event) {


                    $(".pixbasketmoreinfos-inner").slideToggle("fast");

                });


                $("#pixvariantlistingform").VariantCalc();


                // Ajax cart amount display
                function cartAfterRefresh() {
                    var ajaxCartRefreshAfter = controller.ajax_cart_refresh,
                        $cartAmountAfter = $('.cart--amount'),
                        $cartQuantityAfter = $('.cart--quantity');

                    if (!ajaxCartRefreshAfter.length) {
                        return;
                    }

                    $.ajax({
                        'url': ajaxCartRefreshAfter,
                        'dataType': 'json',
                        'success': function (cart) {

                            if (!cart.amount || !cart.quantity) {
                                return;
                            }

                            $cartAmountAfter.html(cart.amount);
                            $cartQuantityAfter.html(cart.quantity).removeClass('is--hidden');

                            if (cart.quantity == 0) {
                                $cartQuantityAfter.addClass('is--hidden');
                            }
                        }
                    });
                }


            });


    </script>

    {/if}
{/block}


{block name="frontend_detail_tabs_navigation_inner"}


    {$smarty.block.parent}

    {*
    *
    * @Dupp: if $theme.showvariantlisting
    *
    *
    *}
    {if $theme.showvariantlisting}

        {if $pixelvariantlisting.pix_am_status == '1'}
            {if $pixelvariants}
                <a href="#" class="tab--link" title="{s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}"
                   data-tabName="variantlisting">

                    {s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}<span
                            class="product--rating-count">{$pixelvariants|@count}</span>

                </a>
            {/if}
        {/if}

    {/if}


{/block}

{block name="frontend_detail_tabs_content_inner"}

    {$smarty.block.parent}

    {if $theme.showvariantlisting}
    {if $pixelvariantlisting.pix_am_status == '1'}
        {if $pixelvariants}
            <div class="tab--container tab-floating">


                {* Description title *}
                {block name="frontend_detail_tabs_content_variantlisting_title"}
                    <div class="tab--header">
                        {block name="frontend_detail_tabs_content_variantlisting_title_inner"}
                            <a href="#" class="tab--title"
                               title="{s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}">{s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}</a>
                            <span
                                    class="product--rating-count">{$pixelvariants|@count}</span>
                        {/block}
                    </div>
                {/block}

                {* Description preview *}
                {block name="frontend_detail_tabs_variantlisting_preview"}
                    <div class="tab--preview">
                        {block name="frontend_detail_tabs_content_variantlisting_preview_inner"}
                            {s name='DetailTabsPixelvariantlistinglistDesc'}Ganz schnell Ihre gew&uuml;nschten Artikel bestellen{/s}
                            <a href="#" class="tab--link"
                               title="{s name="PreviewTextMore"}{/s}">{s name="PreviewTextMore"}{/s}</a>
                        {/block}
                    </div>
                {/block}

                {* Description content *}
                {block name="frontend_detail_tabs_content_variantlisting_description"}
                    <div class="tab--content">
                        {block name="frontend_detail_tabs_content_variantlisting_description_inner"}
                            <div class="buttons--off-canvas">

                                <a href="#"
                                   title="{"{s name="OffcanvasCloseMenu" namespace="frontend/detail/description"}{/s}"|escape}"
                                   class="close--off-canvas">
                                    <i class="icon--arrow-left"></i>
                                    {s name="OffcanvasCloseMenu" namespace="frontend/detail/description"}{/s}
                                </a>

                            </div>
                            {if $pixelvariantlisting.pix_am_status == '1'}
                                {if $pixelvariants}
                                    <div id="pixelvariantlisting-list" class="content--batchdetail pixel-variant-listing-batchdetail tabs-content--variantbuy">
                                        {counter start=0 skip=1 print=false assign=count }

                                        <form method="post" id="pixvariantlistingform" name="pixvariantlistingform"
                                              enctype="multipart/form-data"
                                              action="{url controller=Pixelvariantlistingswf action=SendForm}">

                                            {* Variant Listing table *}
                                            <div class="product--table">

                                                {* Variant Listing table content *}
                                                {block name='frontend_variant_listing_panel'}
                                                    <div class="panel has--border">
                                                        <div class="panel--body is--rounded">

                                                            {* Variant Listing table header *}
                                                            <div class="table--header block-group">
                                                                <div class="panel--th column--option block is--align-left">
                                                                    {s name='DetailTabsPixelvariantlistingOption'}Variante{/s}
                                                                </div>
                                                                <div class="panel--th column--price block is--align-right">
                                                                    {s name='DetailTabsPixelvariantlistingPrice'}Preis{/s}
                                                                </div>
                                                                <div class="panel--th column--instock block is--align-right">
                                                                    {s name='DetailTabsPixelvariantlistingInstock'}Bestand{/s}
                                                                </div>
                                                                <div class="panel--th column--nextstock block is--align-center">
                                                                    {s name='DetailTabsPixelvariantlistingNextstock'}Nächste Lieferung{/s}
                                                                </div>
                                                                <div class="panel--th column--additionalstock block is--align-center">
                                                                    {s name='DetailTabsPixelvariantlistingAdditionalstock'}Weitere Lieferung{/s}
                                                                </div>
                                                                <div class="panel--th column--quantity block is--align-center">
                                                                    {s name='DetailTabsPixelvariantlistingQuantity'}Anzahl{/s}
                                                                </div>
                                                            </div>

                                                            {* Variant Listing table Body *}
                                                            {foreach from=$pixelvariants item=groupedVariant}
                                                                {assign var=image value=$groupedVariant.image.src.0}

                                                                <div class="table--tr block-group row--product">
                                                                    <div class="panel--td column--option">
                                                                        <div class="column--label">{s name="DetailTabsPixelvariantlistingOption"}{/s}:</div>
                                                                        <div class="column--value">
                                                                            {if $groupedVariant.configuratorOptions}
                                                                                {foreach from=$groupedVariant.configuratorOptions item=groupedVariants name=foo}
                                                                                    {if !$smarty.foreach.foo.first},{/if} {$groupedVariants.name}
                                                                                {/foreach}
                                                                            {else}
                                                                                {$groupedVariant.additionalText}

                                                                            {/if}
                                                                        </div>
                                                                    </div>

                                                                    <div class="panel--td column--price is--align-right">
                                                                        <div class="column--label">{s name="DetailTabsPixelvariantlistingPrice"}{/s}:</div>
                                                                        <div class="column--value">{$groupedVariant.price|currency}</div>
                                                                    </div>

                                                                    <div class="panel--td column--instock is--align-right">
                                                                        <div class="column--label">{s name="DetailTabsPixelvariantlistingInstock"}{/s}:</div>
                                                                        <div class="column--value">{$groupedVariant.inStock}</div>
                                                                    </div>

                                                                    <div class="panel--td column--nextstock is--align-center">
                                                                        <div class="column--label">{s name="DetailTabsPixelvariantlistingNextstock"}{/s}:</div>
                                                                        <div class="column--value">
                                                                            {if $groupedVariant.attribute.lieferzeit1}
                                                                                {$groupedVariant.attribute.lieferzeit1}
                                                                                {if $theme.showStock && $groupedVariant.attribute.bestand1} - <strong>{$groupedVariant.attribute.bestand1|escape} {s name="DetailAttributeLieferzeit1Packunit" namespace="frontend/detail/index"}Stück{/s}</strong>{/if}
                                                                            {else}
                                                                                <span><i class="icon--minus3"></i></span>
                                                                            {/if}
                                                                        </div>
                                                                    </div>

                                                                    <div class="panel--td column--additionalstock is--align-center">
                                                                        <div class="column--label">{s name="DetailTabsPixelvariantlistingAdditionalstock"}{/s}:</div>
                                                                        <div class="column--value">
                                                                            {if $groupedVariant.attribute.lieferzeit2}
                                                                                {$groupedVariant.attribute.lieferzeit2}
                                                                                {if $theme.showStock && $groupedVariant.attribute.bestand2} - <strong>{$groupedVariant.attribute.bestand2|escape} {s name="DetailAttributeLieferzeit1Packunit" namespace="frontend/detail/index"}Stück{/s}</strong>{/if}
                                                                            {else}
                                                                                <span><i class="icon--minus3"></i></span>
                                                                            {/if}
                                                                        </div>
                                                                    </div>

                                                                    <div class="panel--td column--quantity">
                                                                        <div class="column--label">{s name="DetailTabsPixelvariantlistingQuantity"}{/s}:</div>
                                                                        <div class="column--value">
                                                                            <input type="hidden" name="sActionIdentifier[{$count}]" value="{$sUniqueRand}"/>
                                                                            <input type="hidden" name="sAddAccessories[{$count}]" id="sAddAccessories" value=""/>
                                                                            <input type="hidden" name="sAdd[{$count}]" id="sAdd" value="{$groupedVariant.number}"/>

                                                                            {if $groupedVariant.maxPurchase}
                                                                                {assign var=maxPurchase value=$groupedVariant.maxPurchase}
                                                                            {else}
                                                                                {assign var=maxPurchase value=$sArticle.maxpurchase}
                                                                            {/if}
                                                                            {if $groupedVariant.inStock < $maxPurchase}
                                                                                {assign var=maxQuantity value=$groupedVariant.inStock+1}
                                                                            {else}
                                                                                {assign var=maxQuantity value= $maxPurchase+1}
                                                                            {/if}

                                                                            {if $groupedVariant.inStock > 0}
                                                                                <select id="sQuantity_{$count}" name="sQuantity[{$count}]" netto="{$groupedVariant.net|replace:",":"."}" brutto="{$groupedVariant.price|replace:",":"."}" ordernumber="{$groupedVariant.number}">
                                                                                    <option value="0">
                                                                                        0{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
                                                                                    {section name="is" start=$sArticle.minpurchase loop=$maxQuantity step=$sArticle.purchasesteps}
                                                                                        <option value="{$smarty.section.is.index}">{$smarty.section.is.index}{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
                                                                                    {/section}
                                                                                </select>
                                                                            {else}
                                                                                <select id="sQuantity_{$count}" name="sQuantity[{$count}]" netto="{$groupedVariant.net|replace:",":"."}" brutto="{$groupedVariant.price|replace:",":"."}" ordernumber="{$groupedVariant.number}" disabled="disabled">
                                                                                    <option value="0">
                                                                                        0{if $sArticle.packunit} {$sArticle.packunit}{/if}
                                                                                    </option>
                                                                                </select>
                                                                            {/if}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {counter}
                                                            {/foreach}

                                                            <div class="table--actions actions--bottom">
                                                                <div class="main--actions">
                                                                    <div class="container">
                                                                        <div class="gesamtview">Gesamtpreis: <span id="gesamt">0,00 {$Mcurrency.currency}</span></div>
                                                                        <button id="basketBatchButton" class="buynow buybox--button block btn is--primary is--icon-right is--center is--large" name="{s name="DetailBuyActionAddName" namespace="frontend/detail/buy"}{/s}">
                                                                            {s name="DetailBuyActionAdd" namespace="frontend/detail/buy"}{/s} <i class="icon--arrow-right"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                {/block}
                                            </div>
                                        </form>

                                        <div class="clear">&nbsp;</div>
                                        <div class="ajax_pixvariantlistingform_result"></div>
                                        <div class="ajax_pixvariantlistingform_errors"></div>
                                    </div>
                                {/if}

                            {/if}
                        {/block}
                    </div>
                {/block}
            </div>
        {/if}

    {/if}
{/if}
{/block}
