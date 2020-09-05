{extends file="parent:frontend/index/footer.tpl"}

{* ... move Newsletter after footer menu *}
{block name='frontend_index_footer_menu'}
    <div class="container">
        {$smarty.block.parent}
    </div>
{/block}

{* Copyright in the footer *}
{block name='frontend_index_footer_copyright'}
    <div class="footer--bottom">
        <div class="container">
            {* Vat info *}
            {block name='frontend_index_footer_vatinfo'}
                <div class="footer--vat-info">
                    <p class="vat-info--text">
                        {if $sOutputNet}
                            {s name='FooterInfoExcludeVat' namespace="frontend/index/footer"}{/s}
                        {else}
                            {s name='FooterInfoIncludeVat' namespace="frontend/index/footer"}{/s}
                        {/if}
                    </p>
                </div>
            {/block}

            {block name='frontend_index_footer_minimal'}
                {include file="frontend/index/footer_minimal.tpl" hideCopyrightNotice=true}
            {/block}

            {* Shopware footer *}
            {block name="frontend_index_shopware_footer"}

                {* Copyright *}
                {block name="frontend_index_shopware_footer_copyright"}
                    <div class="footer--copyright">
                        {s name="IndexCopyright"}{/s}
                    </div>
                {/block}

            {/block}
        </div>
    </div>
{/block}