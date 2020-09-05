{extends file="parent:frontend/detail/image.tpl"}

{block name='frontend_detail_images_image_media'}
    <span class="image--media">
        {if isset($image.thumbnails)}
            {block name='frontend_detail_images_picture_element'}
                <img class="lazyLoad" data-srcset="{$image.thumbnails[1].sourceSet}" alt="{$alt}" itemprop="image" />
            {/block}
        {else}
            {block name='frontend_detail_images_fallback'}
                <img class="lazyLoad" data-src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$alt}" itemprop="image" />
            {/block}
        {/if}
    </span>
{/block}
