{extends file="parent:frontend/index/footer-navigation.tpl"}

{block name="frontend_index_footer_column_newsletter"}
    {* Shipping & Payment *}
    {block name="frontend_index_footer_column_shipping_payment"}
        <div class="footer--column column--shipping-payment block">
            <div class="column--headline">{s name="sFooterPayment"}Zahlen Sie mit{/s}</div>
            <div class="column--content block-group">
                {s name="sFooterPaymentMethods"}<i class="custom-pf--paypal"></i><i class="custom-pf--paypal"></i><i class="custom-pf--paypal"></i>{/s}
            </div>

            <div class="column--headline">{s name="sFooterShipping"}Wir versenden mit{/s}</div>
            <div class="column--content block-group">
                {s name="sFooterShippingMethods"}<i class="custom-pf--paypal"></i><i class="custom-pf--paypal"></i><i class="custom-pf--paypal"></i>{/s}
            </div>
        </div>
    {/block}

    {* Imprint Information *}
    {block name="frontend_index_footer_column_payment"}
        {block name="frontend_index_footer_column_imprint"}
            <div class="footer--column column--imprint block">
                <div class="column--headline is--active">
                    <div class="logo--shop">
                        <picture>
                            <source srcset="{link file=$theme.desktopLogo}" media="(min-width: 78.75em)">
                            <source srcset="{link file=$theme.tabletLandscapeLogo}" media="(min-width: 64em)">
                            <source srcset="{link file=$theme.tabletLogo}" media="(min-width: 48em)">

                            <img class="lazyLoad" data-srcset="{link file=$theme.mobileLogo}" alt="{"{config name=shopName}"|escapeHtml} - {"{s name='IndexLinkDefault' namespace="frontend/index/index"}{/s}"|escape}"/>
                        </picture>
                    </div>
                </div>

                {block name="frontend_index_footer_column_imprint_content"}
                    <div class="column--content is--collapsed" style="display: block;">
                        <p class="column--desc">
                            {s name="FooterImprintDescription"}Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.{/s}
                        </p>
                    </div>
                {/block}
            </div>
        {/block}
    {/block}

{/block}
