{extends file="parent:frontend/checkout/ajax_add_article.tpl"}

{* Article image *}
{block name='checkout_ajax_add_information_image'}
    <div class="article--image block">
        <a href="{$detailLink}" class="link--article-image" title="{$sArticle.articlename|escape}">

            {$image = $sArticle.additional_details.image}
            {$alt = $sArticle.articlename|escape}

            {if $image.description}
                {$alt = $image.description|escape}
            {/if}

            <span class="image--media">
                            {if isset($image.thumbnails)}
                                <img class="lazyLoad" data-srcset="{$image.thumbnails[0].sourceSet}" alt="{$alt}" title="{$alt|truncate:160}" />
                            {else}
                                {block name='frontend_detail_image_fallback'}
                                <img class="lazyLoad" data-src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$alt}" title="{$alt|truncate:160}" />
                            {/block}
                            {/if}
                        </span>
        </a>
    </div>
{/block}
