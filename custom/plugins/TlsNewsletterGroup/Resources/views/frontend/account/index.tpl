{extends file="parent:frontend/account/index.tpl"}

{block name="frontend_account_index_newsletter_settings_content"}
    {if $TlsNewsletterGroupList}
        <div class="panel--body is--wide">
            <form name="frmRegister" method="post" action="{url action=saveNewsletter}" data-newsletter-group="true">
                <input type="hidden" name="newsletter" value="{$sUserData.additional.user.newsletter}" id="newsletter"/>
                <div>
                    {s name="AccountLabelWantNewsletter"}{/s}
                </div>
                <div class="list--checkbox" role="menu">
                    {include file="frontend/tls_newsletter_group/checkboxes.tpl" showChecked=true}
                </div>
                <button type="submit" class="btn is--block is--primary">{s name=tlsSaveButton}Save{/s}</button>
            </form>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
