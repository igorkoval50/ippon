{extends file='parent:frontend/detail/content/tab_navigation.tpl'}

{block name="frontend_detail_index_tabs_navigation"}
    <div class="tab--navigation">
        {block name="frontend_detail_index_tabs_navigation_inner"}
            {block name="frontend_detail_index_related_similiar_tabs"}
                {$smarty.block.parent}
                {* Similar products *}
                {block name="frontend_detail_index_recommendation_tabs_entry_similar_products"}
                {/block}
            {/block}

            {* Customer also bought *}
            {block name="frontend_detail_index_tabs_entry_also_bought"}
{*                {if $showAlsoBought}*}
{*                    <a href="#content--also-bought" title="{s name="DetailRecommendationAlsoBoughtLabel" namespace="frontend/detail/index"}{/s}" class="tab--link">{s name="DetailRecommendationAlsoBoughtLabel" namespace="frontend/detail/index"}{/s}</a>*}
{*                {/if}*}
            {/block}

            {* Customer also viewed *}
            {block name="frontend_detail_index_tabs_entry_also_viewed"}
{*                {if $showAlsoViewed}*}
{*                    <a href="#content--customer-viewed" title="{s name="DetailRecommendationAlsoViewedLabel" namespace="frontend/detail/index"}{/s}" class="tab--link">{s name="DetailRecommendationAlsoViewedLabel" namespace="frontend/detail/index"}{/s}</a>*}
{*                {/if}*}
            {/block}

            {* Related product streams *}
            {block name="frontend_detail_index_tabs_entry_related_product_streams"}
{*                {foreach $sArticle.relatedProductStreams as $key => $relatedProductStream}*}
{*                    <a href="#content--related-product-streams-{$key}" title="{$relatedProductStream.name}" class="tab--link">{$relatedProductStream.name}</a>*}
{*                {/foreach}*}
            {/block}
        {/block}
    </div>
{/block}
