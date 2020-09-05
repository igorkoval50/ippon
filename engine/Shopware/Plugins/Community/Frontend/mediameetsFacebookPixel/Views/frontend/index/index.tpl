{extends file='parent:frontend/index/index.tpl'}

{block name="frontend_index_header_javascript_inline"}
    {$smarty.block.parent}

    {block name='mediameetsFacebookPixelJavaScript'}
        {include file="mediameetsFacebookPixel/index/javascript.tpl"}
    {/block}
{/block}

{block name="frontend_index_page_wrap"}
    {$smarty.block.parent}

    {block name='mediameetsFacebookPixelNotification'}
        {include file="mediameetsFacebookPixel/index/notification.tpl"}
    {/block}
{/block}
