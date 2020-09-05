{extends file='parent:frontend/detail/tabs.tpl'}

{block name="frontend_index_header_css_screen" }

    {$smarty.block.parent}
    <link type="text/css" media="screen, projection" rel="stylesheet"
          href="{link file='frontend/plugins/pixelvariantlistingswf/_resources/styles/pixelvariantlisting_batch.css'}"/>
{/block}


{block name="frontend_index_header_javascript_jquery_lib" }

    {$smarty.block.parent}
    <script type="text/javascript">

        {if $theme.asyncJavascriptLoading}
        document.asyncReady(function () {
            {else}
            $(document).ready(function () {
                {/if}

                var artofBatching = 'batch';
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


                            if (responseJson.success == true) {


                                if (responseJson.stock) {

                                    var setErrors = "";


                                    $.each(responseJson.stock, function (key, value) {

                                        if (value.stockinfo)
                                            setErrors += '<div class="error"><ul><li>' + $.pixvariantlistingform.options.ordernumber + ' ' + value.sAdd + ':' + value.stockinfo + '</li></ul></div>';


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


                                    }


                                });

                                $("#gesamt").html(Total.toFixed(2) + " " + Currency);


                            }
                        });
                    }
                });

                controller.send_pixvariantform = '{url controller=Pixelvariantlistingswf action=SendForm}';


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
{/block}

{block name="frontend_detail_tabs_navigation_inner"}

    {$smarty.block.parent}



    {if $pixelvariantlisting.pix_am_status == '1'}
        {if $pixelvariants}
            <a href="#" class="tab--link" title="{s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}"
               data-tabName="variantlisting">

                {s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}<span
                        class="product--rating-count">{$pixelvariants|@count}</span>

            </a>
        {/if}
    {/if}
{/block}

{block name="frontend_detail_tabs_content_inner" }

    {$smarty.block.parent}

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
                        <div id="pixelvariantlisting-list" class="content--product-reviews pixel-variant-listing-batch">


                            <div id="listing-1col">
                                {counter start=0 skip=1 print=false assign=count }
                                <form method="post" id="pixvariantlistingform" name="pixvariantlistingform"
                                      enctype="multipart/form-data"
                                      action="{url controller=Pixelvariantlisting action=SendForm}">

                                    {foreach from=$pixelvariants item=group}
                                        {if $group.inStock>0}
                                            <input type="hidden" name="sActionIdentifier[{$count}]"
                                                   value="{$sUniqueRand}"/>
                                            <input type="hidden" name="sAddAccessories[{$count}]" id="sAddAccessories"
                                                   value=""/>
                                            <input type="hidden" name="sAdd[{$count}]" id="sAdd"
                                                   value="{$group.number}"/>
                                            <div class="artbox{if $lastitem} last{/if}{if $firstitem} first{/if}">
                                                <div class="inner">

                                                    {* Top *}

                                                    {if $group.highlight}
                                                        <div class="ico_tipp">{se name='ListingBoxTip'}{/se}</div>
                                                    {/if}


                                                    {* New *}

                                                    {if $group.newArticle}
                                                        <div class="ico_new">{se name='ListingBoxNew'}{/se}</div>
                                                    {/if}


                                                    {* ESD article *}

                                                    {if $group.esd}
                                                        <div class="ico_esd">{se name='ListingBoxInstantDownload'}{/se}</div>
                                                    {/if}


                                                    {* Article rating *}

                                                    {if $group.sVoteAverange.averange}
                                                        <div class="star star{($group.sVoteAverange.averange * 2)|round:0}"></div>
                                                    {/if}


                                                    {* Article picture *}


                                                    {assign var=image value=$group.image.src.0}


                                                    <a href="{$sArticle.linkDetails}"
                                                       title="{$sArticle.articleName|escape}"
                                                       class="product--image">
                                            <span class="image--element">
                                                <span class="image--media">
                                                    {if isset($group.image.src)}
                                                        <img srcset="{$image}"
                                                             alt="{$mArticle|escape}"
                                                             title="{$mArticle|escape|truncate:25:""}"/>

{else}


                                                        <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                                                             alt="{$mArticle|escape}"
                                                             title="{$mArticle|escape|truncate:25:""}"/>
                                                    {/if}
                                                </span>

                                            </span>
                                                    </a>


                                                    {* Article name *}

                                                    <div href="{$group.linkDetailsRewrited} {$group.additionalText}"
                                                         class="title"
                                                         title="{$mArticle.name} {$group.additionalText}">{$mArticle.name} {$group.additionalText}
                                                        {foreach from=$group.configuratorOptions item=groups name=foo}{if !$smarty.foreach.foo.first},{/if} {$groups.name}
                                                        {/foreach} </div>


                                                    {* Description *}

                                                    {assign var=size value=270}

                                                    <p class="desc">
                                                        {$group.descriptionLong|strip_tags|truncate:$size}

                                                    </p>


                                                    {* Article Price *}

                                                    <p class="{if $group.pseudoprice}pseudoprice{else}price{/if}">

                                                        <span class="price">{$group.price|currency}</span>
                                                    </p>


                                                    {* Compare and more *}

                                                    <div class="actions">

                                                        {if $group.maxPurchase}
                                                            {assign var=maxPurchase value=$group.maxPurchase}
                                                        {else}
                                                            {assign var=maxPurchase value=$sArticle.maxpurchase}
                                                        {/if}
                                                        {if $group.inStock < $maxPurchase}
                                                            {assign var=maxQuantity value=$group.inStock+1}
                                                        {else}
                                                            {assign var=maxQuantity value= $maxPurchase+1}
                                                        {/if}
                                                        <label for="sQuantity">{s name="DetailBuyLabelQuantity" namespace="frontend/detail/buy"  }Menge{/s}
                                                            :</label>
                                                        <select id="sQuantity_{$count}" name="sQuantity[{$count}]"
                                                                netto="{$group.net|replace:",":"."}"
                                                                brutto="{$group.price|replace:",":"."}"
                                                                ordernumber="{$group.number}">
                                                            <option value="0">
                                                                0{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
                                                            {section name="is" start=$sArticle.minpurchase loop=$maxQuantity step=$sArticle.purchasesteps}
                                                                <option value="{$smarty.section.is.index}">{$smarty.section.is.index}{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
                                                            {/section}
                                                        </select>


                                                    </div>

                                                </div>
                                            </div>
                                            {counter}{/if}{/foreach}
                            </div>

                            {* Buy now button *}
                            <div class="container">
                                <div class="gesamtview">Gesamtpreis: <span id="gesamt">0,00 {$Mcurrency.currency}</span>
                                </div>


                                <button id="basketBatchButton"
                                        class="buynow buybox--button block btn is--primary is--icon-right is--center is--large"
                                        name="{s name="DetailBuyActionAddName" namespace="frontend/detail/buy"}{/s}">
                                    {s name="DetailBuyActionAdd" namespace="frontend/detail/buy"}{/s} <i
                                            class="icon--arrow-right"></i>
                                </button>

                                </form>


                            </div>

                            <div class="ajax_pixvariantlistingform_result"></div>
                            <div class="ajax_pixvariantlistingform_errors"></div>

                        </div>
                    {/block}
                </div>
            {/block}
        {/if}

    {/if}


    </div>
{/block}