{block name="frontend_ajaxsearch_index_inner"}
    <ul class="results--list">

        {* synonym banner for SwagFuzzy *}
        {block name="frontend_ajaxsearch_index_similar_list_banners"}
            {if $swagFuzzySynonymGroups}
                {foreach $swagFuzzySynonymGroups as $synonymGroup}
                    {block name="frontend_ajaxsearch_index_similar_list_banner"}

                        {if $synonymGroup.ajaxSearchHeader || $synonymGroup.ajaxSearchBanner || $synonymGroup.ajaxSearchDescription}
                            {if $synonymGroup@first}
                                <li class="fuzzy-similar-entry">
                                    <div class="search--fuzzy">
                            {/if}

                                        <div class="fuzzy--synonym-banner is--align-center">
                                            <a href="{if $synonymGroup.ajaxSearchLink}{$synonymGroup.ajaxSearchLink}{else}#{/if}" title="{$synonymGroup.groupName|escape|truncate:155}">
                                                {block name="frontend_ajaxsearch_index_similar_list_banner_header"}
                                                    {if $synonymGroup.ajaxSearchHeader}
                                                        {$synonymGroup.ajaxSearchHeader}
                                                    {/if}
                                                {/block}

                                                {block name="frontend_ajaxsearch_index_similar_list_banner_image"}
                                                    {if $synonymGroup.ajaxSearchBanner}
                                                        {$thumbnails = $synonymGroup.ajaxSearchBanner->getThumbnails()}
                                                        <picture>
                                                            <source srcset="{link file=$thumbnails[2]->getSource()}" media="(min-width: 78em)">
                                                            <source srcset="{link file=$thumbnails[1]->getSource()}" media="(min-width: 48em)">

                                                            <img class="fuzzy--synonym-media" srcset="{link file=$thumbnails[0]->getSource()}" alt="{$synonymGroup.groupName|escape|truncate:155}"/>
                                                        </picture>
                                                    {/if}
                                                {/block}

                                                {block name="frontend_ajaxsearch_index_similar_list_banner_description"}
                                                    {if $synonymGroup.ajaxSearchDescription}
                                                        {$synonymGroup.ajaxSearchDescription}
                                                    {/if}
                                                {/block}
                                            </a>
                                        </div>

                            {if $synonymGroup@first}
                                    </div>
                                </li>
                            {/if}
                        {/if}

                    {/block}
                {/foreach}
            {/if}
        {/block}

        {if $swagFuzzyFacets}
            {$keywordFacets = $swagFuzzyFacets[0]->getFacetResults()}
        {/if}
        {if $keywordFacets[0] && $keywordFacets[0]->getFacetName() == 'similar_requests'}
            {$similarRequestFacet = $keywordFacets[0]}

            {block name="frontend_ajaxsearch_index_similar_list_label"}
                <li class="fuzzy--similar-entry fuzzy--similar-label">
                    <span class="ajax-search--fuzzy-similar-requests-label">{$similarRequestFacet->getLabel()}</span>
                </li>
            {/block}

            {block name="frontend_ajaxsearch_index_similar_list_entries"}
                {* Add similiar words to search result *}
                {foreach $similarRequestFacet->getValues() as $key => $value}
                    {block name="frontend_ajaxsearch_index_similar_list_entry"}
                        <li class="fuzzy--similar-entry{if $value@last || $key == 2} fuzzy--is-last-item{/if} result--item">
                                <a class="search-result--link" href="{url controller=search sSearch={$value->getLabel()|escape}}" title="{$value->getLabel()}">
                                    <span class="fuzzy--entry-name">
                                        <div class="is--align-left">
                                            {$value->getLabel()|escape}
                                        </div>
                                    </span>
                                </a>
                        </li>
                    {/block}

                    {* Display only 3 synonym suggestions *}
                    {if $key == 2}
                        {break}
                    {/if}
                {/foreach}
            {/block}
        {/if}

        {foreach $sSearchResults.sResults as $search_result}

            {* Each product in the search result *}
            {block name="search_ajax_list_entry"}
                <li class="list--entry block-group result--item">
                    <a class="search-result--link" href="{$search_result.link}" title="{$search_result.name|escape:'html'}">

                        {* Product image *}
                        {block name="search_ajax_list_entry_media"}
                            <span class="entry--media block">
                                {if $search_result.image.thumbnails[0]}
                                    <img srcset="{$search_result.image.thumbnails[0].sourceSet}" alt="{$search_result.name|escape:'html'}" class="media--image">
                                {else}
                                    <img src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{"{s name='ListingBoxNoPicture' namespace='frontend/search/ajax'}{/s}"|escape}" class="media--image">
                                {/if}
                            </span>
                        {/block}

                        {* Product name *}
                        {block name="search_ajax_list_entry_name"}
                        <span class="entry--name block">
                            {$search_result.name}
                        </span>
                        {/block}

                        {* Product price *}
                        {block name="search_ajax_list_entry_price"}
                            <span class="entry--price block">
                                    {$sArticle = $search_result}
                                {*reset pseudo price value to prevent discount boxes*}
                                {$sArticle.has_pseudoprice = 0}

                                {block name="search_ajax_list_entry_price_main"}
                                    {include file="frontend/listing/product-box/product-price.tpl"}
                                {/block}

                                {block name="search_ajax_list_entry_price_unit"}
                                    {include file="frontend/search/product-price-unit.tpl"}
                                {/block}
                                </span>
                        {/block}
                    </a>
                </li>
            {/block}
        {/foreach}

        {* Link to show all founded products using the built-in search *}
        {block name="search_ajax_all_results"}
            <li class="entry--all-results block-group result--item">

                {* Link to the built-in search *}
                {block name="search_ajax_all_results_link"}
                    <a href="{url controller='search' sSearch=$sSearchRequest.sSearch}" class="search-result--link entry--all-results-link block">
                        <i class="icon--arrow-right"></i>
                        {s name="SearchAjaxLinkAllResults" namespace="frontend/search/ajax"}{/s}
                    </a>
                {/block}

                {* Result of all founded products *}
                {block name="search_ajax_all_results_number"}
                    <span class="entry--all-results-number block">
                    {$sSearchResults.sArticlesCount} {s name='SearchAjaxInfoResults' namespace='frontend/search/ajax'}{/s}
                </span>
                {/block}
            </li>
        {/block}
    </ul>
{/block}
