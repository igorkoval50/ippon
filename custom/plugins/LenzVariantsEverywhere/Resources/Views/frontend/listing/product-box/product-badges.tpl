{extends file="parent:frontend/listing/product-box/product-badges.tpl"}

{namespace name="frontend/listing/box_article"}

{block name='frontend_listing_box_article_discount'}
	{if {controllerName|lower} != 'search'
	|| ({config namespace="LenzVariantsEverywhere" name="showVariantsInSearch" default=true} && {controllerName|lower} == 'search')
	}
		{if $sArticle.lenzVariantsEverywhereVariantPrice < $sArticle.lenzVariantsEverywhereVariantPseudoPrice || (!$sArticle.lenzVariantsEverywhereVariantPrice && $sArticle.price < $sArticle.pseudoprice)}
			<div class="product--badge badge--discount">
				<i class="icon--percent2"></i>
			</div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
