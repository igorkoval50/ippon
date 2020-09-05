{extends file="parent:frontend/blog/images.tpl"}

{block name='frontend_blog_images_main_image'}
    {$alt = $sArticle.title|escape}

    {if $sArticle.preview.description}
        {$alt = $sArticle.preview.description|escape}
    {/if}

    <div class="blog--detail-images block">
        <a href="{$sArticle.preview.source}"
           data-lightbox="true"
                {if $sArticle.preview.extension === 'svg'}
                    data-is-svg='true'
                {/if}
           title="{$alt}"
           class="link--blog-image">

            {if $sArticle.preview.source}
                <img srcset="{$sArticle.preview.source}"
                     src="{$sArticle.preview.source}"
                     class="blog--image panel has--border is--rounded"
                     alt="{$alt}"
                     title="{$alt|truncate:160}"/>
            {else}
                <img srcset="{media path=$sArticle.media[0].media.path}"
                     src="{media path=$sArticle.media[0].media.path}"
                     class="blog--image panel has--border is--rounded"
                     alt="{$alt}"
                     title="{$alt|truncate:160}"/>
            {/if}
        </a>
    </div>
{/block}
