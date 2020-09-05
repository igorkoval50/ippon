{extends file="parent:frontend/blog/listing_sidebar.tpl"}

{* @Dupp: Rearrange order Subscribe Content*}

{* Blog navigation *}
{block name="frontend_blog_index_navigation"}
    <div class="blog--navigation block-group">

        {* Blog filter *}
        {block name='frontend_blog_index_filter'}
            {include file="frontend/blog/filter.tpl"}
        {/block}

        {* Subscribe Atom + RSS *}
        {block name='frontend_blog_index_subscribe'}
            <div class="blog--subscribe has--border is--rounded filter--group block">

                {* Subscribe headline *}
                {block name="frontend_blog_index_subscribe_headline"}
                    <div class="blog--subscribe-headline blog--sidebar-title collapse--header blog-filter--trigger">
                        {s name="BlogSubscribe"}{/s}<span class="filter--expand-collapse collapse--toggler"></span>
                    </div>
                {/block}

                {* Subscribe Content *}
                {block name="frontend_blog_index_subscribe_content"}
                    <div class="blog--subscribe-content blog--sidebar-body collapse--content">
                        <ul class="filter--list list--unstyled">
                            {block name="frontend_blog_index_subscribe_entry_rss"}
                                <li class="filter--entry"><a class="filter--entry-link" href="{$sCategoryContent.rssFeed}" title="{$sCategoryContent.description|escape}">{s namespace="frontend/blog/index" name="BlogLinkRSS"}{/s}</a></li>
                            {/block}

                            {block name="frontend_blog_index_subscribe_entry_atom"}
                                <li class="filter--entry is--last"><a class="filter--entry-link" href="{$sCategoryContent.atomFeed}" title="{$sCategoryContent.description|escape}">{s namespace="frontend/blog/index" name="BlogLinkAtom"}{/s}</a></li>
                            {/block}
                        </ul>
                    </div>
                {/block}
            </div>
        {/block}

    </div>
{/block}