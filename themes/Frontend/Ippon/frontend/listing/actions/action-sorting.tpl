{extends file="parent:frontend/listing/actions/action-sorting.tpl"}

{* Sorting field *}
{block name='frontend_listing_actions_sort_field'}
    {$listingMode = {config name=listingMode}}

    <div class="sort--select select-field">
        <select name="{$shortParameters.sSort}"
                class="sort--field action--field"
                data-auto-submit="true"
                {if $listingMode != 'full_page_reload'}data-loadingindicator="false"{/if}>

            <option value="0" style="display: none;">{s name="ListingLabelSortSelect"}Sortieren{/s}</option>
            {foreach $sortings as $sorting}
                {block name="frontend_listing_actions_sort_field_release"}
                    <option value="{$sorting->getId()}"{if $sSort eq $sorting->getId()} selected="selected"{/if}>{$sorting->getLabel()}</option>
                {/block}
            {/foreach}

            {block name='frontend_listing_actions_sort_values'}{/block}
        </select>
    </div>
{/block}

{* Sorting label *}
{block name='frontend_listing_actions_sort_label'}
    {$smarty.block.parent}
    <label class="sort--label action--label action--label-inside">{s name='ListingLabelSortSelect'}{/s}</label>
{/block}
