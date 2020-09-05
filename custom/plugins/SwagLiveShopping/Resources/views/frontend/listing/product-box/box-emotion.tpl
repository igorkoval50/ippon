{extends file='parent:frontend/listing/product-box/box-emotion.tpl'}

{block name="frontend_listing_box_article_content"}
	{if $sArticle.attributes.live_shopping && $sArticle.attributes.live_shopping->get('live_shopping')}
		{$sArticle.liveShopping = $sArticle.attributes.live_shopping->get('live_shopping')}
		{$sArticle.liveShopping.showDescription = {config name="showDescriptionInTheListing" namespace="SwagLiveShopping"}}
	{/if}

	{if $sArticle.liveShopping}
		{$liveShopping = $sArticle.liveShopping}
		<div class="liveshopping--listing"
			{block name="frontend_listing_box_product_liveshopping_content_data"}
				data-live-shopping-listing-product="true"
				data-validTo="{$liveShopping.validTo}"
				data-liveShoppingId="{$liveShopping.id}"
			{/block}
		>
			{$smarty.block.parent}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{* Liveshopping listing badge *}
{block name='frontend_listing_box_article_discount'}
	{block name='frontend_listing_liveshopping_box_article_discount'}
		{include file='frontend/swag_live_shopping/listing/liveshopping-listing-badge.tpl'}
	{/block}

    {$smarty.block.parent}
{/block}

{block name='frontend_listing_box_article_price_info'}
	{block name='frontend_listing_liveshopping_box_article_price_info'}
		{include file='frontend/swag_live_shopping/listing/liveshopping-box-article-price.tpl'}
	{/block}
{/block}

{block name="frontend_listing_box_article_description"}
	{if {config name=showDescriptionInTheListing} == true || !$sArticle.liveShopping}
		{$smarty.block.parent}
	{else}
		<div class="product--description"></div>
	{/if}
{/block}
