{extends file="parent:frontend/detail/images.tpl"}

{block name='frontend_detail_image_thumbs_images_img'}
    <img data-srcset="{$image.thumbnails[0].sourceSet}"
         alt="{s name="DetailThumbnailText" namespace="frontend/detail/index"}{/s}: {$alt}"
         title="{s name="DetailThumbnailText" namespace="frontend/detail/index"}{/s}: {$alt|truncate:160}"
         class="thumbnail--image lazyLoad" />
{/block}
