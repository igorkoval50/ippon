{extends file="frontend/index/sidebar.tpl"}

{block name="frontend_index_sidebar"}
    {if $theme.sidebarFilter}
        {block name='frontend_listing_sidebar'}
            <div class="listing--sidebar">
                {$smarty.block.parent}

                <div class="sidebar-filter">
                    <div class="sidebar-filter--content">
                        {include file="frontend/listing/actions/action-filter-panel.tpl"}
                    </div>
                </div>

                {include file="frontend/listing/sidebar_box.tpl"}
            </div>
        {/block}
    {else}
        <div class="listing--sidebar">
            {$smarty.block.parent}
            {include file="frontend/listing/sidebar_box.tpl"}
        </div>
    {/if}
{/block}