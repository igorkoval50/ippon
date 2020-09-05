{extends file="parent:frontend/index/footer-navigation.tpl"}

{block name="frontend_index_footer_column_newsletter_privacy"}
    {if $TlsNewsletterGroupList}
        {include file="frontend/tls_newsletter_group/group-list.tpl"}
    {/if}
    {$smarty.block.parent}
{/block}
