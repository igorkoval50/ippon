{block name="frontend_detail_liveshopping_data"}
    {if $liveShopping}
        {block name="frontend_liveshopping_detail"}
            <div class="liveshopping--details"
                {block name="frontend_listing_box_product_liveshopping_content_data"}
                    data-live-shopping-listing-product="true"
                    data-validTo="{$liveShopping.validTo}"
                    data-liveShoppingId="{$liveShopping.id}"
                {/block}
            >
                {* Liveshopping counter *}
                {block name="frontend_liveshopping_detail_counter"}
                    <div class="counter is--align-center">
                        <div class="counter--time {if $liveShopping.limited === 1}is--stock{/if}">

                            {* Liveshopping counter headline *}
                            {block name="frontend_liveshopping_detail_counter_headline"}
                                <div class="counter--headline">
                                    {s name="sLiveHeadline" namespace="frontend/live_shopping/main"}{/s}
                                </div>
                            {/block}

                            {* Liveshopping counter *}
                            {block name='frontend_liveshopping_detail_counter_include'}
                                {include file='frontend/swag_live_shopping/_includes/liveshopping-counter.tpl'}
                            {/block}
                        </div>

                        {* Liveshopping stock *}
                        {block name='frontend_liveshopping_detail_stock'}
                            {if $liveShopping.limited === 1}
                                {include file='frontend/swag_live_shopping/_includes/liveshopping-stock.tpl'}
                            {/if}
                        {/block}

                    </div>
                {/block}

                {* Liveshopping content with price and discount *}
                {block name='frontend_liveshopping_detail_content'}
                    <div class="liveshopping--prices">

                        {* Icon, regular price, discount price, unit price *}
                        {block name='frontend_liveshopping_detail_pricing_include'}
                            {include file="frontend/swag_live_shopping/detail/liveshopping-detail-pricing.tpl"}
                        {/block}

                        {block name='frontend_liveshopping_detail_pricing_meta_valid_until'}
                            <meta itemprop="priceValidUntil" content="{$liveShopping.validTo|date_format:"%Y-%m-%dT%T"}">
                        {/block}
                    </div>
                {/block}
            </div>
        {/block}
    {/if}
{/block}
