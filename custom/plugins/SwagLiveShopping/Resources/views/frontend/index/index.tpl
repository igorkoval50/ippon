{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_page_wrap"}
    <div
        data-live-shopping-listing="true"
        data-liveShoppingListingUpdateUrl="{url module='widgets' controller='LiveShopping' action='getLiveShoppingListingData'}"
        data-currencyFormat="{0|currency}"
    >
        {$smarty.block.parent}
    </div>
{/block}


