{extends file="parent:frontend/checkout/finish.tpl"}

{block name='frontend_index_logo'}
    {$smarty.block.parent}

    <div class="logo--shop logo--print block">
        <a class="logo--link" href="{url controller='index'}" title="{"{config name=shopName}"|escapeHtml} - {"{s name='IndexLinkDefault' namespace="frontend/index/index"}{/s}"|escape}">
            <picture>
                <img src="{link file=$theme.printLogo}" alt="{"{config name=shopName}"|escapeHtml} - {"{s name='IndexLinkDefault' namespace="frontend/index/index"}{/s}"|escape}" />
            </picture>
        </a>
    </div>
{/block}

{block name='frontend_checkout_finish_teaser_title'}
    <h2 class="panel--title teaser--title is--underline is--align-center">{s name="FinishHeaderThankYou"}{/s} {$sShopname|escapeHtml}!</h2>
{/block}
