{extends file='parent:frontend/detail/tabs.tpl'}

{block name="frontend_index_header_css_screen" }

    {$smarty.block.parent}
    <link type="text/css" media="screen, projection" rel="stylesheet"
          href="{link file='frontend/plugins/pixelvariantlistingswf/_resources/styles/pixelvariantlisting.css'}"/>
{/block}

{block name="frontend_detail_tabs_navigation_inner"}

    {$smarty.block.parent}



    {if $pixelvariantlisting.pix_am_status == '1'}
        {if $pixelvariants}
            <a href="#" class="tab--link" title="{s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}"  data-tabName="variantlisting">

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
                           title="{s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}">{s name='DetailTabsPixelvariantlistinglist'}Schnelleinkauf{/s}</a><span
                            class="product--rating-count">{$pixelvariants|@count}</span>
                    {/block}
                </div>
            {/block}

            {* Description preview *}
            {block name="frontend_detail_tabs_variantlisting_preview"}
                <div class="tab--preview">
                    {block name="frontend_detail_tabs_content_variantlisting_preview_inner"}
                        {s name='DetailTabsPixelvariantlistinglistDesc'}Ganz schnell Ihre gew&uuml;nschten Artikel bestellen{/s}
                        <a href="#" class="tab--link" title="{s name="PreviewTextMore"}{/s}">{s name="PreviewTextMore"}{/s}</a>

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


                        <div id="pixelvariantlisting-list" class="content--product-reviews">


                            <div id="listing-1col">


                                {foreach from=$pixelvariants item=group}
                                    {if $group.inStock>0}





                                    <form method="post" action="{url controller=checkout action=addArticle}" class="basketform"
                                          data-addarticleurl="{url controller=checkout action=ajaxAddArticleCart}"
                                          data-showmodal="false" data-eventname="submit" data-add-article="true"
                                          action="http://dev5.pixeleyes.de/checkout/addArticle" method="post"
                                          name="sAddToBasket">



                                        <input type="hidden" name="sActionIdentifier" value="{$sUniqueRand}"/>
                                        <input type="hidden" name="sAddAccessories" id="sAddAccessories" value=""/>
                                        <input type="hidden" name="sAdd" value="{$group.number}"/>


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

                                                <div href="{$group.linkDetailsRewrited} {$group.additionalText}" class="title"
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
                                                    <select id="sQuantity" name="sQuantity">
                                                        {section name="is" start=$sArticle.minpurchase loop=$maxQuantity step=$sArticle.purchasesteps}
                                                            <option value="{$smarty.section.is.index}">{$smarty.section.is.index}{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
                                                        {/section}
                                                    </select>

                                                    {* Buy now button *}



                                                    <button  id="basketButton" class="buynow buybox--button block btn is--primary is--icon-right is--center is--large" name="{s name="DetailBuyActionAddName" namespace="frontend/detail/buy"}{/s}">
                                                        {s name="DetailBuyActionAdd" namespace="frontend/detail/buy"}{/s} <i class="icon--arrow-right"></i>
                                                    </button>

                                                </div>

                                            </div>
                                        </div>
                                        </form>
                                    {/if}
                                {/foreach}
                            </div>
                        </div>
                    {/block}
                </div>
            {/block}




        {/if}

    {/if}


    </div>
{/block}