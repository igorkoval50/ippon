{extends file='parent:frontend/index/index.tpl'}

{block name='frontend_index_header_meta_robots'}nofollow,noindex{/block}

{block name='frontend_index_content_left'}{/block}

{block name="frontend_index_content"}
    <div class="mediameetsFacebookPixel--content mediameetsFacebookPixel--choiceByLink content block" data-action="{$action}">
        {block name="frontend_mediameets_facebook_pixel_message"}
            <div class="mediameetsFacebookPixel--messages">
                {include file='frontend/_includes/messages.tpl' type='success' content=$content}
            </div>
        {/block}
    </div>
{/block}