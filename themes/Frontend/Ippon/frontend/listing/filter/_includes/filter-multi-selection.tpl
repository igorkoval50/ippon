{extends file="parent:frontend/listing/filter/_includes/filter-multi-selection.tpl"}

{block name="frontend_listing_filter_facet_multi_selection_label"}
    <label class="filter-panel--label"
           for="__{$facet->getFieldName()|escape:'htmlall'}__{$option->getId()|escape:'htmlall'}">

        {if $facet|is_a:'\Shopware\Bundle\SearchBundle\FacetResult\MediaListFacetResult'}
            {$mediaFile = {link file='frontend/_public/src/img/no-picture.jpg'}}
            {if $option->getMedia()}
                {$mediaFile = $option->getMedia()->getFile()}
            {/if}

            <img class="filter-panel--media-image lazyLoad" data-src="{$mediaFile}" alt="{$option->getLabel()|escape:'htmlall'}" />
        {else}
            {$option->getLabel()|escape}
        {/if}
    </label>
{/block}
