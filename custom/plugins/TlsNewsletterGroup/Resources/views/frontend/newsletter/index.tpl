{extends file="parent:frontend/newsletter/index.tpl"}

{block name="frontend_newsletter_form_input_zip_and_city"}
    {$smarty.block.parent}

    {if $TlsNewsletterGroupList}
        <div class="list--checkbox" role="menu">
            {include file="frontend/tls_newsletter_group/checkboxes.tpl" showChecked=true}
        </div>
    {/if}
{/block}
