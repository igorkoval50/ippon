{foreach from=$articles item=article name=liveArticle}
    {* Version check for the new image thumbnail structure introduced in shopware 5+ *}
    {assign var=image value=$article.image.thumbnails.0.source}

    {$sArticle=$article}
    {$liveShopping=$article["liveShopping"]}
    {if $smarty.foreach.liveArticle.first}
    <tr>
    {/if}
        <td width="240" align="center" valign="top" style="padding: 0px 15px 10px; border: 1px solid #DFDFDF">
            <table width="100%" height="290" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td height="24" align="center" style="border-left: 1px solid #dfdfdf; border-right: 1px solid #dfdfdf; border-bottom: 1px solid #dfdfdf;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px; font-size: 14px; font-weight: bold; color: #444;">
                        {s name="liveShoppingTo" namespace="backend/swag_newsletter/main"}{/s} {$liveShopping.validTo|date_format:"%d.%m.%G %R"}
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding-top: 25px" height="145">
                        <div style="display: block; height: 145px;">
                        {if $image}
                            <a target="_blank" href="{url controller=detail sArticle=$article.articleID}" title="{$article.articleName|escape}">
                                <img src="{$image}" title="{$article.articleName}" style="max-height: 100%;" />
                            </a>
                        {else}
                            <a target="_blank" href="{url controller=detail sArticle=$article.articleID}" title="{$article.articleName|escape}">
                                <img src="{link file="frontend/_resources/images/no_picture.jpg" fullPath}" title="{$article.articleName}" />
                            </a>
                        {/if}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td height="30" align="center">
                        <a title="{$article.articleName}" target="_blank" href="{url controller=detail sArticle=$article.articleID}" style="display: block;font-size: 14px; color: #000000; font-weight: 700;">{$article.articleName|truncate:35}</a>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="91" bgcolor="#ff0000" style="margin-top: 15px; color: #ffffff;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="60" align="center">
                                    {if $liveShopping.type eq 1}
                                        <img src="{link file='frontend/_resources/images/icon_clock.png' fullPath}" alt="icon_clock" width="30" height="30" />
                                    {elseif $liveShopping.type eq 2}
                                        <img src="{link file='frontend/_resources/images/icon_down.png' fullPath}" alt="icon_clock" width="30" height="30" />
                                    {else}
                                        <img src="{link file='frontend/_resources/images/icon_up.png' fullPath}" alt="icon_clock" width="30" height="30" />
                                    {/if}
                                </td>
                                <td width="180">
                                    <span style="font-size:1.0em;margin-bottom:5px;margin-top:0px; color: #ffffff;">
                                        {if $liveShopping.type===1}
                                            <small>{s name="reducedPrice" namespace="frontend/live_shopping/main"}{/s} <em style="text-decoration: line-through;">{$liveShopping.startPrice|currency}{s namespace="frontend/listing/box_article" name="Star"}*{/s}</em></small><br />
                                        {elseif $liveShopping.type===2}
                                            <small>{s name="reducedPrice" namespace="frontend/live_shopping/main"}{/s} <em style="text-decoration: line-through;">{$liveShopping.startPrice|currency}{s namespace="frontend/listing/box_article" name="Star"}*{/s}</em></small><br />
                                        {else}
                                            <small>{s name="reducedPrice" namespace="frontend/live_shopping/main"}{/s} <em style="text-decoration: line-through;">{$liveShopping.endPrice|currency}{s namespace="frontend/listing/box_article" name="Star"}*{/s}</em></small><br />
                                        {/if}
                                        <strong style="font-size: large; text-shadow: 0 2px 0 #88050f;" >{$liveShopping.currentPrice|currency}{s namespace="frontend/listing/box_article" name="Star"}*{/s}</strong><br />
                                    </span>
                                </td>
                            </tr>
                            {if $sArticle.purchaseunit != $sArticle.referenceunit}
                            <tr>
                                <td colspan="2" align="center">
                                    <span style="font-size: 0.8em; color: #ffffff;">
                                        <span style="font-size: 0.8em; margin: 0; line-height: 1.1em;">
                                            {if $sArticle.referenceunit}
                                                <strong>{s name="DetailDataInfoContent" namespace="frontend/detail/data"}{/s}</strong> {$sArticle.purchaseunit} {$sArticle.sUnit.description} (<span>{$liveShopping.referenceUnitPrice|currency}</span> {s name="Star" namespace="frontend/listing/box_article"}{/s} / {$sArticle.referenceunit} {$sArticle.sUnit.description})
                                            {/if}
                                        </span>
                                    </span>
                                </td>
                            </tr>
                            {/if}
                            <tr>
                                <td colspan="2" align="center">
                                    {if $liveShopping.type === 1}
                                        <small style="color: #ffffff; display:block; text-shadow:0 2px 0 #88050f; font-size: 0.8em;">{s name="sLiveSave" namespace="frontend/live_shopping/main"}{/s} {$liveShopping.percentage|number_format:2:',': '.'}%</small>
                                    {elseif $liveShopping.type === 2}
                                        <small style="color: #ffffff; display:block; text-shadow:0 2px 0 #88050f; font-size: 0.8em;">{s name="sLivePriceFalls" namespace="frontend/live_shopping/main"}{/s} {$liveShopping.perMinute|currency}{s namespace="frontend/listing/box_article" name="Star"}*{/s}</small>
                                    {else}
                                        <small style="color: #ffffff; display:block; text-shadow:0 2px 0 #88050f; font-size: 0.8em;">{s name="sLivePriceRises" namespace="frontend/live_shopping/main"}{/s} {$liveShopping.perMinute|currency}{s namespace="frontend/listing/box_article" name="Star"}*{/s}</small>
                                    {/if}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    {if $smarty.foreach.liveArticle.last}
    </tr>
    {/if}
{/foreach}
