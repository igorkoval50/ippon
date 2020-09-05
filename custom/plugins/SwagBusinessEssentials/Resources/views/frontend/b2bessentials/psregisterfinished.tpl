{extends file='frontend/b2bessentials/pslogin.tpl'}

{* Hide shop navigation *}
{block name='frontend_index_shop_navigation'}
    {if !$privateRegister || $privateShoppingConfig['unlockAfterRegister']}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_index_navigation_categories_top'}
    {if !$privateRegister || $privateShoppingConfig['unlockAfterRegister']}
        {$smarty.block.parent}
    {/if}
{/block}

{* Hide top bar *}
{block name='frontend_index_top_bar_container'}
    {if !$privateRegister || $privateShoppingConfig['unlockAfterRegister']}
        {$smarty.block.parent}
    {/if}
{/block}

{* footer *}
{block name='frontend_index_footer'}
    {if !$privateRegister || $privateShoppingConfig['unlockAfterRegister']}
        {$smarty.block.parent}
    {else}
        {block name='frontend_index_register_footer'}
            {include file='frontend/index/footer_minimal.tpl'}
        {/block}
    {/if}
{/block}

{* hide left content *}
{block name='frontend_index_content_left'}{/block}

{* content *}
{block name='frontend_index_content'}
    {block name='business_essentials_confirm'}
        <div class="panel has--border business-essentials--confirm">

            {block name='business_essentials_confirm_panel_header'}
                <div class="panel--title is--underline">{s name='PrivateRegisterConfirmHeader' namespace='frontend/account/login'}{/s}</div>
            {/block}

            {block name='business_essentials_confirm_panel_body'}
                <div class="panel--body is--wide">

                    {block name='business_essentials_confirm_message'}
                        {include file='frontend/_includes/messages.tpl' type='info' content="{s name='PrivateRegisterConfirmMessage' namespace='frontend/account/login'}{/s}"}
                    {/block}

                    {block name='business_essentials_confirm_link'}
                        <a href="{url controller='index' action='index'}" class="btn is--primary is--icon-left">
                            {block name='business_essentials_confirm_link_icon'}
                                <i class="icon--arrow-left is--large"></i>{s name='AccountLogoutButton' namespace='frontend/account/ajax_logout'}Zurück{/s}
                            {/block}
                        </a>
                    {/block}
                </div>
            {/block}
        </div>
    {/block}
{/block}
