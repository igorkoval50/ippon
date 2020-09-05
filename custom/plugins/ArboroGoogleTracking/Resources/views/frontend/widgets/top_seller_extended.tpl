{extends file="parent:widgets/listing/top_seller.tpl"}

{block name="widgets_listing_top_seller"}
    {$smarty.block.parent}
    
    <noscript data-tag="arboro-google-tracking">
        {if $trackingType == "UA"}
            {foreach name=articles from=$sCharts item=sArticle}
                {literal}
                    ga('ec:addImpression', {
                        'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                        'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                        'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                        {/literal}{if $brandTracking }{literal}
                        'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                        {/literal}{/if}{literal}
                        'list': 'Topseller',
                        'position': {/literal}{$smarty.foreach.articles.iteration}{literal}
                    });
                {/literal}
            {/foreach}
            {literal}ga('send', 'event', 'topseller', 'view');{/literal}
        {elseif $trackingType == "GTM"}
            {$dataLayerName|escape}{literal}.push({
              'ecommerce': {
                'impressions': [{/literal}
                {foreach name=articles from=$sCharts item=sArticle}{literal}
                 {
                   'name': '{/literal}{$sArticle.articleName|escape}{literal}',
                   'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
                   'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                    {/literal}{if $brandTracking }{literal}
                   'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                    {/literal}{/if}{literal}
                   'list': 'Topseller',
                   'position': {/literal}{$smarty.foreach.articles.iteration}{literal}
                 },
                {/literal}{/foreach}
                {literal}]
              }
            });
            {/literal}
        {/if}
    </noscript>
{/block}
