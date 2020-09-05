{extends file="parent:frontend/index/sidebar-categories.tpl"}

{block name="frontend_index_categories_left_entry"}
    <li class="navigation--entry{if $category.flag} is--active{/if}{if $category.subcategories} has--sub-categories{/if}{if $category.childrenCount} has--sub-children{/if}" role="menuitem">
        <a class="navigation--link{if $category.flag} is--active{/if}{if $category.subcategories} has--sub-categories{/if}{if $category.childrenCount} link--go-forward{/if}{if $category.attribute.highlight} is--highlighted{/if}"
           href="{$category.link}"
           data-categoryId="{$category.id}"
           data-fetchUrl="{url module=widgets controller=listing action=getCategory categoryId={$category.id}}"
           title="{$category.description|escape}"
           {if $category.external && $category.externalTarget}target="{$category.externalTarget}"{/if}>
            {$category.description}

            {if $category.childrenCount}
                <span class="is--icon-right">
                    <i class="icon--arrow-right"></i>
                </span>
            {/if}
        </a>
        {block name="frontend_index_categories_left_entry_subcategories"}
            {if $category.subcategories}
                {call name=categories categories=$category.subcategories level=$level+1}
            {/if}
        {/block}
    </li>
{/block}