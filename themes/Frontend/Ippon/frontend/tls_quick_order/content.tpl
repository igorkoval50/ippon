{block name="frontend_detail_tabs_content_quick_order"}
    {if $tlsQuickOrder}
        <div class="tab--container">
            {block name="frontend_detail_tabs_content_quick_order_inner"}

                {* Title *}
                {block name="frontend_detail_tabs_quick_order_title"}
                    <div class="tab--header">
                        {block name="frontend_detail_tabs_quick_order_title_inner"}
                            <a href="#" class="tab--title" title="{s name='DetailTabsQuickOrder'}Quick Order{/s}">{s name='DetailTabsQuickOrder'}{/s}</a>
                            {block name="frontend_detail_tabs_quick_order_title_count"}
                                <span class="product--rating-count">{$tlsQuickOrder.count}</span>
                            {/block}
                        {/block}
                    </div>
                {/block}

                {* Preview *}
                {block name="frontend_detail_tabs_quick_order_preview"}
                    <div class="tab--preview">
                        {block name="frontend_detail_tabs_quick_order_preview_inner"}
                            {s name="QuickOrderPreviewText"}Order your favorite products now{/s}
                        {/block}
                    </div>
                {/block}

                {* Content *}
                {block name="frontend_detail_tabs_quick_order_content"}
                    <div id="tab--quick-order" class="tab--content">
                        {block name="frontend_detail_tabs_quick_order_content_inner"}

                            {* Offcanvas buttons *}
                            {block name='frontend_detail_description_buttons_offcanvas'}
                                <div class="buttons--off-canvas">
                                    {block name='frontend_detail_description_buttons_offcanvas_inner'}
                                        {s name="OffcanvasCloseMenu" namespace="frontend/detail/description" assign="snippetOffcanvasCloseMenu"}{/s}
                                        <a href="#" title="{$snippetOffcanvasCloseMenu|escape}" class="close--off-canvas">
                                            <i class="icon--arrow-left"></i>
                                            {s name="OffcanvasCloseMenu" namespace="frontend/detail/description"}{/s}
                                        </a>
                                    {/block}
                                </div>
                            {/block}

                            {block name="frontend_detail_tab_quick_order_content"}
                                <div class="tlscontent--description">

                                    {* Headline *}
                                    {block name="frontend_detail_tab_quick_order_title"}
                                    {/block}

                                    {block name="frontend_detail_tab_quick_order"}
                                        <div class="tlsquickorder">
                                            <form action="{url controller=TlsQuickOrder action=addToCart}" data-TlsQuickOrder="true">
                                                <div class="tlsquickorder--body">
                                                    {* Text *}
                                                    {block name="frontend_detail_tab_quick_order_text"}
                                                        {include file="frontend/tls_quick_order/table.tpl"}
                                                    {/block}
                                                </div>

                                                {block name="frontend_detail_tab_quick_order_cart_button"}
                                                    {include file="frontend/tls_quick_order/cart_button.tpl"}
                                                {/block}
                                            </form>
                                        </div>
                                    {/block}

                                </div>
                            {/block}

                        {/block}
                    </div>
                {/block}

            {/block}
        </div>
    {/if}
{/block}