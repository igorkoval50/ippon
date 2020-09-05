{extends file="parent:widgets/index/shop_menu.tpl"}
{* Language switcher *}
{block name="frontend_index_actions_active_shop_language_form_select"}
    {if $theme.languageSwitcher}
        <a href="#" class="btn is--link language-switcher--button is--icon-right">
            <i class="icon--arrow-down"></i>
            <span class="language--display">{s name="languageSwitcherText" namepsace="widgetsIndexShopMenu"}Sprache{/s}</span>
        </a>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}