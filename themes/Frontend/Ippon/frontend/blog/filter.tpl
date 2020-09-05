{* @Dupp: ... remove Author, sort tags to top *}

{if $sFilterTags && $sFilterTags|@count > 1}

    {* Filter by tags *}
    {block name='frontend_blog_filter_tags'}
        <div class="blog--filter blog--filter-tags has--border is--rounded filter--group block">

            {* Filter headline *}
            {block name="frontend_blog_filter_tags_headline"}
                <div class="blog--filter-headline blog--sidebar-title collapse--header blog-filter--trigger">
                    {s name="BlogHeaderFilterTags"}{/s}<span class="filter--expand-collapse collapse--toggler"></span>
                </div>
            {/block}

            {* Filter content *}
            {block name="frontend_blog_filter_tags_content"}
                <div class="blog--filter-content blog--sidebar-body collapse--content">
                    <ul class="filter--list list--unstyled">
                        {foreach $sFilterTags as $tag}
                            {if !$tag.removeProperty}
                                {if $smarty.get.sFilterTags==$tag.name|urlencode}
                                    {$filterTagsActive=true}
                                    <li class="filter--entry is--active"><a href="{$tag.link}" title="{$tag.name|escape}" class="filter--entry-link is--active is--bold">{$tag.name} ({$tag.tagsCount})</a></li>
                                {else}
                                    <li class="filter--entry{if $tag@last} is--last{/if}"><a href="{$tag.link}" class="filter--entry-link" title="{$tag.name|escape}">{$tag.name} ({$tag.tagsCount})</a></li>
                                {/if}
                            {elseif $filterTagsActive}
                                <li class="filter--entry close"><a href="{$tag.link}" class="filter--entry-link" title="{"{s name='FilterLinkDefault' namespace='frontend/listing/filter_properties'}{/s}"|escape}">{s name='FilterLinkDefault' namespace='frontend/listing/filter_properties'}{/s}</a></li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
            {/block}
        </div>
    {/block}
{/if}

{if $sFilterAuthor && $sFilterAuthor|@count > 1}
    {* ... remove Filter by author *}
    {block name='frontend_blog_filter_author'}{/block}
{/if}

{if $sFilterDate && $sFilterDate|@count > 1}

    {* Filter by date *}
    {block name='frontend_blog_filter_date'}
        <div class="blog--filter blog--filter-date has--border is--rounded filter--group block">

            {* Filter headline *}
            {block name="frontend_blog_filter_date_headline"}
                <div class="blog--filter-headline blog--sidebar-title collapse--header blog-filter--trigger">
                    {s name="BlogHeaderFilterDate"}{/s}<span class="filter--expand-collapse collapse--toggler"></span>
                </div>
            {/block}

            {* Filter content *}
            {block name="frontend_blog_filter_date_content"}
                <div class="blog--filter-content blog--sidebar-body collapse--content">
                    <ul class="filter--list list--unstyled">
                        {foreach $sFilterDate as $date}
                            {if !$date.removeProperty}
                                {if $smarty.get.sFilterDate==$date.dateFormatDate}
                                    {$filterDateActive=true}
                                    <li class="filter--entry is--active"><a href="{$date.link}" class="filter--entry-link is--active is--bold" title="{$date.dateFormatDate|escape}">{$date.dateFormatDate|date_format:"{s name="BlogHeaderFilterDateFormat"}{/s}"} ({$date.dateCount})</a></li>
                                {else}
                                    <li class="filter--entry{if $date@last} is--last{/if}"><a href="{$date.link}" class="filter--entry-link" title="{$date.dateFormatDate|escape}">{$date.dateFormatDate|date_format:"{s name="BlogHeaderFilterDateFormat"}{/s}"} ({$date.dateCount})</a></li>
                                {/if}
                            {elseif $filterDateActive}
                                <li class="filter--entry close"><a href="{$date.link}" class="filter--entry-link" title="{"{s name='FilterLinkDefault' namespace='frontend/listing/filter_properties'}{/s}"|escape}">{s name='FilterLinkDefault' namespace='frontend/listing/filter_properties'}{/s}</a></li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
            {/block}
        </div>
    {/block}
{/if}