
{extends file="parent:frontend/listing/product-box/box-big-image.tpl"}

{block name='frontend_listing_box_article_image_media'}
    {$smarty.block.parent}


    {block name='tab10_hoverimage'}
        {if $lhIsActive}
            <div class="tab10--listing--hover-image">
                {foreach from=$sArticle.attributes.hover_images->toArray() item=image name=articleImages}
                    {if ($sArticle.attributes.hover_images->toArray()|@count >= 2 and $image@index == 1)}
                        {if $lhNoLoadImage}
                            <img srcset="{$image.thumbnails[$lhProductBoxBigImgSource].src}" data-srcog="{$sArticle.image.thumbnails[$lhProductBoxBigImgSource].sourceSet}" class="{if $lhFadeImage} fade{/if}"/>
                        {else}
                            <img srcset="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-srcog="{$sArticle.image.thumbnails[$lhProductBoxBigImgSource].sourceSet}" data-srcset="{$image.thumbnails[$lhProductBoxBigImgSource].src}" class="{if $lhFadeImage} fade{/if}{if !$lhNoLoadImage} lh-load{/if}"/>
                        {/if}
                    {/if}
                {/foreach}
            </div>
        {/if}
    {/block}
{/block}
