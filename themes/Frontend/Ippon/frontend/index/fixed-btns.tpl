
<div class="fixed-btns">
    <div class="fixed-btns--wrap block-group">
        {block name='frontend_index_fixedBtns_first'}
            {if $theme.linkText1 && $theme.linkId1}
                <div class="fixed-btn--item block">
                    <a href="{url controller=custom sCustom={$theme.linkId1}}" title="{$theme.linkText1}">
                        {if $theme.linkIcon1}<i class="{$theme.linkIcon1}"></i>{/if}
                        <span>{$theme.linkText1}</span>
                    </a>
                </div>
            {/if}
        {/block}
        {block name='frontend_index_fixedBtns_first'}
            {if $theme.linkText2 && $theme.linkId2}
                <div class="fixed-btn--item block">
                    <a href="{url controller=custom sCustom={$theme.linkId2}}" title="{$theme.linkText2}">
                        {if $theme.linkIcon2}<i class="{$theme.linkIcon2}"></i>{/if}
                        <span>{$theme.linkText2}</span>
                    </a>
                </div>
            {/if}
        {/block}
        {block name='frontend_index_fixedBtns_first'}
            {if $theme.linkText3 && $theme.linkId3}
                <div class="fixed-btn--item block">
                    <a href="{url controller=custom sCustom={$theme.linkId3}}" title="{$theme.linkText3}">
                        {if $theme.linkIcon3}<i class="{$theme.linkIcon3}"></i>{/if}
                        <span>{$theme.linkText3}</span>
                    </a>
                </div>
            {/if}
        {/block}
    </div>
</div>