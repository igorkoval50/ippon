<noscript data-tag="arboro-google-tracking">
    {$dataLayerName|escape}{literal}.push({
          'ecommerce': {
            'impressions': [{/literal}
            {foreach name=articles from=$sArticles item=sArticle}{literal}
             {
               'name': '{/literal}{$sArticle.articleName|escape}{literal}',
               'id': '{/literal}{$sArticle.ordernumber|escape}{literal}',
               'price': '{/literal}{$sArticle.price_numeric|escape}{literal}',
                {/literal}{if $brandTracking }{literal}
               'brand': '{/literal}{$sArticle.supplierName|escape}{literal}',
                {/literal}{/if}{literal}
               'list': 'Also {/literal}{$template}{literal} articles',
               'position': {/literal}{$smarty.foreach.articles.iteration}{literal}
             },
            {/literal}{/foreach}
            {literal}]
          }
        });
    {/literal}
</noscript>