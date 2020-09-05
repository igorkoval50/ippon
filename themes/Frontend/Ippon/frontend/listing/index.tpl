{extends file="parent:frontend/listing/index.tpl"}

{block name='frontend_index_content_main'}
    {if !empty($sCategoryContent.media)}
        <div class="category--banner">
            <picture class="banner--image">
                <source srcset="{$sCategoryContent.media.thumbnails[1].sourceSet}" media="(min-width: 48em)">

                {* Fallback *}
                <img srcset="{$sCategoryContent.media.thumbnails[0].sourceSet}" class="banner--image"{if $sCategoryContent.cmsHeadline} alt="{$sCategoryContent.cmsHeadline|escape}" {else} alt="{$sCategoryContent.name|escape}" {/if} />
            </picture>
        </div>
    {/if}

    {$smarty.block.parent}
{/block}

