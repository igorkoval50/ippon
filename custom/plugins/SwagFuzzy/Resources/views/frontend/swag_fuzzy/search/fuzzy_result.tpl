{block name="frontend_search_index_result_list_headline"}
    <div class="search--fuzzy panel">
        <div class="panel--body">
            {foreach $swagFuzzySynonymGroups as $synonymGroup}
                {if $synonymGroup.normalSearchHeader || $synonymGroup.normalSearchBanner || $synonymGroup.normalSearchDescription}
                    <div class="fuzzy--synonym-banner is--align-center">
                        <a href="{if $synonymGroup.normalSearchLink}{$synonymGroup.normalSearchLink}{else}#{/if}" title="{$synonymGroup.groupName|escape|truncate:155}">

                            {block name="frontend_search_index_banner_header"}
                                {if $synonymGroup.normalSearchHeader}
                                    {$synonymGroup.normalSearchHeader}
                                {/if}
                            {/block}

                            {block name="frontend_search_index_banner_image"}
                                {if $synonymGroup.normalSearchBanner}
                                    {$thumbnails = $synonymGroup.normalSearchBanner->getThumbnails()}
                                    <picture>
                                        <source srcset="{link file=$thumbnails[2]->getSource()}" media="(min-width: 78em)">
                                        <source srcset="{link file=$thumbnails[1]->getSource()}" media="(min-width: 48em)">

                                        <img class="fuzzy--synonym-media" srcset="{link file=$thumbnails[0]->getSource()}" alt="{$synonymGroup.groupName|escape|truncate:155}"/>
                                    </picture>
                                {/if}
                            {/block}

                            {block name="frontend_search_index_banner_description"}
                                {if $synonymGroup.normalSearchDescription}
                                    {$synonymGroup.normalSearchDescription}
                                {/if}
                            {/block}

                        </a>
                    </div>
                {/if}
            {/foreach}

            {block name="frontend_search_index_similar_headline"}
                <h1 class="fuzzy--listing-headline is--align-center">
                    {s name='SearchHeadline' namespace='frontend/search/fuzzy'}{/s}
                </h1>
            {/block}

            {foreach $keywordFacet->getFacetResults() as $result}
                {block name="frontend_index_search_similar_results_{$result->getFacetName()}"}
                    <div class="fuzzy--listing-{$result->getFieldName()} is--align-center">
                        <span class="fuzzy--listing-label">{$result->getLabel()}</span>

                        {$count = $result->getValues()|count-1}
                        {foreach $result->getValues() as $key => $value}
                            {if $count > $key}
                                <a href="{url controller=search sSearch={$value->getLabel()|escape}}">{$value->getLabel()|escape}</a>
                                |
                            {else}
                                <a href="{url controller=search sSearch={$value->getLabel()|escape}}">{$value->getLabel()|escape}</a>
                            {/if}
                        {/foreach}
                    </div>
                {/block}
            {/foreach}
        </div>
    </div>
{/block}
