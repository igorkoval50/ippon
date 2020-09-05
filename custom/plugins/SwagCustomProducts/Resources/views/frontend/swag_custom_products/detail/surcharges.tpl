{namespace name="frontend/detail/option"}

{block name="frontend_swag_custom_price_overview"}
    <script id="overview-template" type="text/x-handlebars-template">
        {block name="frontend_swag_custom_price_overview_surcharges"}
        <div class="panel has--border custom-products--surcharges">
            {block name="frontend_swag_custom_price_overview_surcharges_title"}
                <div class="panel--title is--underline">{s name="detail/overview/surcharge_price"}{/s}</div>
            {/block}
            {block name="frontend_swag_custom_price_overview_surcharges_body"}
                <div class="panel--body">
                    {block name="frontend_swag_custom_price_overview_surcharges_list"}
                        <ul class="custom-products--overview-list custom-products--list-surcharges">
                            {block name="frontend_swag_custom_price_overview_surcharges_base_listitem"}
                                <li class="custom-products--overview-base">
                                    &nbsp;&nbsp;{s name="detail/overview/base_price"}{/s}
                                    <span class="custom-products--overview-price">{literal}{{formatPrice basePrice}}{/literal}</span>
                                </li>
                            {/block}
                            {literal}{{#surcharges}}{/literal}
                            {block name="frontend_swag_custom_price_overview_surcharges_listitem"}
                                <li>
                                    {literal}
                                    {{#if hasParent}}
                                        &emsp;
                                    {{/if}}
                                    {/literal}
                                    {block name="frontend_swag_custom_price_overview_surcharges_listitem_name"}
                                        <span class="custom-products--overview-name">{literal}{{name}}{/literal}</span>
                                    {/block}
                                    {block name="frontend_swag_custom_price_overview_surcharges_listitem_price"}
                                        <span class="custom-products--overview-price">{literal}{{formatPrice price}}{/literal}</span>
                                    {/block}
                                </li>
                            {/block}
                            {literal}{{/surcharges}}{/literal}
                            {block name="frontend_swag_custom_price_overview_surcharges_total_listitem"}
                                <li class="custom-products--overview-total">
                                    {s name="detail/overview/total_surcharges"}{/s}{if $sArticle.packunit}{$sArticle.packunit}{else}{s namespace="frontend/detail/option" name="basket/per_unit"}unit{/s}{/if}
                                    <span class="custom-products--overview-price">{literal}{{formatPrice totalUnitPrice}}{/literal}</span>
                                </li>
                            {/block}
                        </ul>
                    {/block}
                </div>
            {/block}
            {block name="frontend_swag_custom_price_overview_once_surcharges_body_wrapper"}
            {literal}{{#if hasOnceSurcharges}}{/literal}
                <div class="panel--title is--underline">{s name="detail/overview/once_price"}{/s}</div>
                {block name="frontend_swag_custom_price_overview_once_surcharges_body"}
                    <div class="panel--body">
                    {block name="frontend_swag_custom_price_overview_once_surcharges_list"}
                        <ul class="custom-products--overview-list custom-products--list-once">
                            {literal}{{#onceprices}}{/literal}
                            {block name="frontend_swag_custom_price_overview_once_surcharges_listitem"}
                                <li>
                                    {literal}
                                        {{#if hasParent}}
                                        &emsp;
                                        {{/if}}
                                    {/literal}
                                    {block name="frontend_swag_custom_price_overview_once_surcharges_listitem_name"}
                                        <span class="custom-products--overview-name">{literal}{{name}}{/literal}</span>
                                    {/block}
                                    {block name="frontend_swag_custom_price_overview_once_surcharges_listitem_price"}
                                        <span class="custom-products--overview-price">{literal}{{formatPrice price}}{/literal}</span>
                                    {/block}
                                </li>
                            {/block}
                            {literal}{{/onceprices}}{/literal}
                            {block name="frontend_swag_custom_price_overview_once_surcharges_total_listitem"}
                                <li class="custom-products--overview-total custom-products--overview-once">
                                    {s name="detail/overview/total_once"}{/s}
                                    <span class="custom-products--overview-price">{literal}{{formatPrice totalPriceOnce}}{/literal}</span>
                                </li>
                            {/block}
                        </ul>
                    {/block}
                {/block}
                </div>
            {literal}{{/if}}{/literal}
            {/block}

            <div class="panel--title is--underline">{s name="detail/overview/price_total"}{/s}</div>

            {block name="frontend_swag_custom_price_overview_surcharges_total_total"}
                <div class="panel--body">
                    <ul class="custom-products--overview-list custom-products--list-once">
                        {block name="frontend_swag_custom_price_overview_surcharges_total_total_listitem"}
                            <li class="custom-products--overview-total custom-products--overview-once">
                                {s name="detail/overview/total_price"}{/s}
                                <span class="custom-products--overview-price">{literal}{{formatPrice total}}{/literal}</span>
                            </li>
                        {/block}
                    </ul>
                </div>
            {/block}
        </div>
        {/block}
    </script>
{/block}