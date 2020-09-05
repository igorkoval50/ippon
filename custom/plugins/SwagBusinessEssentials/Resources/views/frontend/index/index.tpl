{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_page_wrap"}
    {if $requireReload}
        <script>
            (function() {
                window.location.reload();
            })();
        </script>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{* Hides the shop-navigation including the wishlist-, cart- and account-icon *}
{block name='frontend_index_shop_navigation'}
    {if $minimalView}
        <div class="is--hidden">
            {$smarty.block.parent}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{* Hides the category overview in the header *}
{block name='frontend_index_navigation_categories_top'}
    {if $minimalView}
        <div class="is--hidden">
            {$smarty.block.parent}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{* Hides the language- and currency-switcher, as well as the compare icon *}
{block name='frontend_index_top_bar_container'}
    {if $minimalView}
        <div class="is--hidden">
            {$smarty.block.parent}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{* Shows the minimal footer, if necessary *}
{block name="frontend_index_footer"}
    {if !$minimalView}
        {$smarty.block.parent}
    {else}
        {block name="frontend_index_footer_swag_business_essentials"}
            {include file="frontend/index/footer_minimal.tpl"}
        {/block}
    {/if}
{/block}