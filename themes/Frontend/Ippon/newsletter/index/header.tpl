{extends file="parent:newsletter/index/header.tpl"}

{block name="newsletter_header_content_logo"}
    {* align left needed for old outlook versions *}
    {if $theme.printLogo}
        <img align="left" width="110" src="{link file=$theme.printLogo fullPath}" alt="{s name="NewsletterHeaderLogoDescription"}{/s}" style="max-width: 25%;"/>
    {else}
        <img align="left" width="110" src="{link file='frontend/_public/src/img/logos/logo--mobile.png' fullPath}" alt="{s name="NewsletterHeaderLogoDescription"}{/s}" style="max-width: 25%;"/>
    {/if}
{/block}