{namespace name="frontend/plugins/mediameetsFacebookPixel/notification"}

{if in_array($mediameetsFacebookPixel.config.privacyMode, ['optin', 'optout'])}
    <div id="mediameetsFacebookPixel--notification" class="is--hidden">
        <div class="container">
            <div class="mediameetsFacebookPixel--message">
                {if $mediameetsFacebookPixel.config.privacyMode == 'optin'}
                    {s name="opt-in/sentence"}{/s}
                {else}
                    {s name="opt-out/sentence"}{/s}
                {/if}
                <a href="{s name="more-link"}{/s}" class="mediameetsFacebookPixel--moreButton">{s name="more"}{/s}</a>
            </div>
            <div class="mediameetsFacebookPixel--actions">
                <a href="{url controller=facebookPixelPrivacy action=close}" class="mediameetsFacebookPixel--closeButton">{s name="close"}{/s}</a>
                <a href="{url controller=facebookPixelPrivacy action=$mediameetsFacebookPixel.config.linkAction}" class="btn is--primary is--small mediameetsFacebookPixel--actionButton">
                    {if $mediameetsFacebookPixel.config.privacyMode == 'optin'}
                        {s name="opt-in/button"}{/s}
                    {else}
                        {s name="opt-out/button"}{/s}
                    {/if}
                </a>
            </div>
        </div>
    </div>
{/if}