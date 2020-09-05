{block name='frontend_detail_image_default_image_slider_item_custom'}
    <div class="image-box">
        <a class="fancybox zoom" data-fancybox="gallery" href="{$sArticle.image.thumbnails[1].source}" rel="group">

            {block name='frontend_detail_image_default_image_element_custom'}

                {$alt = $sArticle.articleName|escape}

                {if $sArticle.image.description}
                    {$alt = $sArticle.image.description|escape}
                {/if}

                {if isset($sArticle.image.thumbnails)}
                    {block name='frontend_detail_image_default_picture_element_custom'}
                        <img width='555' height='320' src="{$sArticle.image.thumbnails[1].source}"
                             alt="{$alt}"
                             itemprop="image" />
                    {/block}
                {else}
                    {block name='frontend_detail_image_fallback_custom'}
                        <img width='555' height='320' src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$alt}" itemprop="image" />
                    {/block}
                {/if}

            {/block}
        </a>
    </div>
{/block}

{assign var="counter" value=$smarty.foreach.destkopimages.iteration}
{foreach from=$sArticle.images item=image name="destkopimages"}
    {*    {if $version == "desktop" && $counter > 5 || $version == "mobile" && $counter > 3 }*}
    {if $counter > 5}
        {break}
    {/if}
    {block name='frontend_detail_images_image_slider_item_custom'}
        <div class="image-box">
            <a class="fancybox zoom" data-fancybox="gallery"
                    href="{$image.thumbnails[1].sourceSet}">

                {block name='frontend_detail_images_image_element_custom'}

                    {$alt = $sArticle.articleName|escape}

                    {if $image.description}
                        {$alt = $image.description|escape}
                    {/if}

                    {$imageMediaClasses = 'image--media'}

                    {if isset($image.thumbnails)}
                        {block name='frontend_detail_images_picture_element_custom'}
                            <img width='555' height='320' src="{$image.thumbnails[1].sourceSet}" alt="{$alt}" />
                        {/block}
                    {else}
                        {block name='frontend_detail_images_fallback_custom'}
                            <img width='555' height='320' src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$alt}" itemprop="image" />
                        {/block}
                    {/if}

                {/block}
            </a>
        </div>
    {/block}
{/foreach}
