{block name='frontend_compare_property_list_swagcustomproducts_col'}
    <li class="list--entry entry--custom-products">
        {if $sArticle.swagCustomProducts}
            {s name="CompareCustomProductsCheck"}{/s}
            <i class="icon icon--check"></i>
        {else}
            {s name="CompareCustomProductsCross"}{/s}
            <i class="icon icon--cross"></i>
        {/if}
    </li>
{/block}
