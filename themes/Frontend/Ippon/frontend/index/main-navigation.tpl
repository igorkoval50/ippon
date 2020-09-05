{extends file="parent:frontend/index/main-navigation.tpl"}

{block name='frontend_index_navigation_categories_top_home'}{/block}

{block name='frontend_index_navigation_categories_top_entry'}
    {if !$sCategory.hideTop}
        <li class="navigation--entry{if $sCategory.flag} is--active{/if}{if $sCategory.attribute.separate} is--separated{/if}" role="menuitem">
            {block name='frontend_index_navigation_categories_top_link'}
                <a class="navigation--link{if $sCategory.flag} is--active{/if}{if $sCategory.attribute.highlight} is--highlighted{/if}" href="{$sCategory.link}" title="{$sCategory.description}" itemprop="url"{if $sCategory.external && $sCategory.externalTarget} target="{$sCategory.externalTarget}"{/if}>
                    {if $sCategory.attribute.icon}
                        <i class="{$sCategory.attribute.icon}"></i>
                    {/if}
                    <span itemprop="name">{$sCategory.description}</span>
                </a>
            {/block}
        </li>
    {/if}
{/block}