{extends file='parent:frontend/_includes/cookie_permission_note.tpl'}
{namespace name="frontend/cookiepermission/index"}

{block name="cookie_permission_content_text"}
    {$smarty.block.parent}

    {if {config name="cookie_note_mode"} == 1 && $cookieSwOptimizeView}
        {s name="cookieSwDeny"}
            Diese Cookies können über den Button
            <a href="#" class="cookie-permission--decline-button cookie--decline-in-text">{$swCookieDeny}</a>abgelehnt werden.
        {/s}
    {/if}
{/block}

{block name="cookie_permission_decline_button"}
    {if {config name="cookie_note_mode"} == 1 && $cookieSwOptimizeView}{else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="cookie_permission_accept_button_fixed"}
    {if {config name="cookie_note_mode"} == 1 && $cookieSwOptimizeView}
        <a href="#" class="cookie-permission--configure-button btn is--large is--center" data-openConsentManager="true">
            {s name="cookiePermission/configure"}{/s}
        </a>
        {if {config name="cookie_show_button"}}
            <a href="#" class="cookie-permission--accept-button btn is--primary is--large is--center">
                {s name="cookiePermission/acceptAll"}{/s}
            </a>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
