{extends file="parent:frontend/listing/product-box/product-price.tpl"}

{block name='frontend_listing_box_article_price_default'}
	{if {config name="showVariantPrice" namespace="LenzVariantsEverywhere"}
		&& (
			{controllerName|lower} != 'search'
			|| ({config namespace="LenzVariantsEverywhere" name="showVariantsInSearch" default=true} && {controllerName|lower} == 'search')
		)
	}
		<span class="price--default is--nowrap{if $sArticle.lenzVariantsEverywhereVariantPrice < $sArticle.lenzVariantsEverywhereVariantPseudoPrice || (!$sArticle.lenzVariantsEverywhereVariantPrice && $sArticle.price < $sArticle.pseudoprice)} is--discount{/if}">

			{if $sArticle.lenzVariantsEverywhereVariantPriceFrom}
				{s namespace="frontend/listing/box_article" name='ListingBoxArticleStartsAt'}{/s}
			{/if}
			{if $sArticle.lenzVariantsEverywhereVariantPrice}
				{$sArticle.lenzVariantsEverywhereVariantPrice|currency}
			{else}
				{$sArticle.price|currency}
			{/if}

			{s name="Star"}{/s}
        </span>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name='frontend_listing_box_article_price_discount'}
	{if {controllerName|lower} != 'search'
	|| ({config namespace="LenzVariantsEverywhere" name="showVariantsInSearch" default=true} && {controllerName|lower} == 'search')
	}
		{if $sArticle.lenzVariantsEverywhereVariantPrice < $sArticle.lenzVariantsEverywhereVariantPseudoPrice || (!$sArticle.lenzVariantsEverywhereVariantPrice && $sArticle.price < $sArticle.pseudoprice)}
			<span class="price--pseudo">

                {block name='frontend_listing_box_article_price_discount_before'}
					{s name="priceDiscountLabel" namespace="frontend/detail/data"}{/s}
				{/block}

			<span class="price--discount is--nowrap">
				{if $sArticle.lenzVariantsEverywhereVariantPrice < $sArticle.lenzVariantsEverywhereVariantPseudoPrice}
					{$sArticle.lenzVariantsEverywhereVariantPseudoPrice|currency}
				{elseif !$sArticle.lenzVariantsEverywhereVariantPrice && $sArticle.price < $sArticle.pseudoprice}
					{$sArticle.pseudoprice|currency}
				{/if}
				{s name="Star"}{/s}
			</span>

			{block name='frontend_listing_box_article_price_discount_after'}
				{s name="priceDiscountInfo" namespace="frontend/detail/data"}{/s}
			{/block}
		</span>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}

{/block}
