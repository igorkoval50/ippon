{extends file="parent:frontend/checkout/ajax_add_article.tpl"}

{block name='frontend_checkout_ajax_add_article_middle'}
{$smarty.block.parent}

    {block name='frontend_arboro_tracking_checkout_ajax_add_article'}
        <noscript data-tag="arboro-google-tracking">
            {if $trackingType == "UA"}
                {foreach key=similarPosition item=sSimilarArticle from=$sCrossSimilarShown}
                    {literal}
                        ga('ec:addImpression', {
                            'name': '{/literal}{$sSimilarArticle.articleName|escape}{literal}',
                            'id': '{/literal}{$sSimilarArticle.ordernumber|escape}{literal}',
                            'price': '{/literal}{$sSimilarArticle.price_numeric|escape}{literal}',
                            {/literal}{if $brandTracking }{literal}
                            'brand': '{/literal}{$sSimilarArticle.supplierName|escape}{literal}',
                            {/literal}{/if}{literal}
                            'list': 'Similar Articles',
                            'position': {/literal}{$similarPosition}{literal}
                        });
                    {/literal}
                {/foreach}
                {foreach key=similarPosition item=sSimilarArticle from=$sCrossBoughtToo}
                    {literal}
                        ga('ec:addImpression', {
                            'name': '{/literal}{$sSimilarArticle.articleName|escape}{literal}',
                            'id': '{/literal}{$sSimilarArticle.ordernumber|escape}{literal}',
                            'price': '{/literal}{$sSimilarArticle.price_numeric|escape}{literal}',
                            {/literal}{if $brandTracking }{literal}
                            'brand': '{/literal}{$sSimilarArticle.supplierName|escape}{literal}',
                            {/literal}{/if}{literal}
                            'list': 'Also bought articles',
                            'position': {/literal}{$similarPosition}{literal}
                        });
                    {/literal}
                {/foreach}
                {literal}
                ga('ec:addProduct', {
                    'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                    'name': '{/literal}{$sArticle.articlename|escape}{literal}',
                    {/literal}{if $brandTracking }{literal}
                    'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                    {/literal}{/if}{literal}
                    'variant': '{/literal}{$sArticle.additional_details.additionaltext|escape}{literal}',
                    'price': '{/literal}{$sArticle.price|escape}{literal}',
                    'quantity': '{/literal}{$sArticle.quantity|escape}{literal}'
                });
                {/literal}
                ga('ec:setAction', 'add');
                ga('send', 'event', 'UX', 'click', 'add to cart');
            {elseif $trackingType == "GTM"}
                {$dataLayerName|escape}{literal}.push({
                    'ecommerce': {
                        'impressions': [{/literal}
                            {foreach key=similarPosition item=sSimilarArticle from=$sCrossSimilarShown}{literal}
                            {
                                'name': '{/literal}{$sSimilarArticle.articleName|escape}{literal}',
                                'id': '{/literal}{$sSimilarArticle.ordernumber|escape}{literal}',
                                'price': '{/literal}{$sSimilarArticle.price_numeric|escape}{literal}',
                                {/literal}{if $brandTracking }{literal}
                                'brand': '{/literal}{$sSimilarArticle.supplierName|escape}{literal}',
                                {/literal}{/if}{literal}
                                'list': 'Similar Articles',
                                'position': {/literal}{$similarPosition}{literal}
                            },
                            {/literal}{/foreach}
                            {foreach key=similarPosition item=sSimilarArticle from=$sCrossBoughtToo}{literal}
                            {
                                'name': '{/literal}{$sSimilarArticle.articleName|escape}{literal}',
                                'id': '{/literal}{$sSimilarArticle.ordernumber|escape}{literal}',
                                'price': '{/literal}{$sSimilarArticle.price_numeric|escape}{literal}',
                                {/literal}{if $brandTracking }{literal}
                                'brand': '{/literal}{$sSimilarArticle.supplierName|escape}{literal}',
                                {/literal}{/if}{literal}
                                'list': 'Also bought articles',
                                'position': {/literal}{$similarPosition}{literal}
                            },
                            {/literal}{/foreach}
                            {literal}],
                        'add': {
                            'products': [{
                                'name': '{/literal}{$sArticle.articlename|escape}{literal}',
                                'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                                'price': '{/literal}{$sArticle.price|escape}{literal}',
                                'quantity': '{/literal}{$sArticle.quantity|escape}{literal}'
                            }]
                        }
                    }
                });
                {/literal}
            {/if}
        </noscript>
    {/block}
{/block}
