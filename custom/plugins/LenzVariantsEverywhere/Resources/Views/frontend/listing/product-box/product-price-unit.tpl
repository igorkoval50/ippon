{namespace name="frontend/listing/box_article"}


{if {controllerName|lower} != 'search' || ({config namespace="LenzVariantsEverywhere" name="showVariantsInSearch" default=true} && {controllerName|lower} == 'search')}
	<div class="price--unit">

		{* Price is based on the purchase unit *}
		{if $sArticle.lenzVariantsEverywhereVariantPurchaseUnit && $sArticle.lenzVariantsEverywhereVariantPurchaseUnit != 0}

			{* Unit price label *}
			{block name='frontend_listing_box_article_unit_label'}
				<span class="price--label label--purchase-unit is--bold is--nowrap">
					{s name="ListingBoxArticleContent"}{/s}
				</span>
			{/block}

			{* Unit price content *}
			{block name='frontend_listing_box_article_unit_content'}
				<span class="is--nowrap">
					{$sArticle.lenzVariantsEverywhereVariantPurchaseUnit} {$sArticle.lenzVariantsEverywhereVariantUnit}
				</span>
			{/block}
		{/if}

		{* Unit price is based on a reference unit *}
		{if $sArticle.lenzVariantsEverywhereVariantPurchaseUnit && $sArticle.lenzVariantsEverywhereVariantPurchaseUnit != $sArticle.lenzVariantsEverywhereVariantReferenceUnit}

			{* Reference unit price content *}
			{block name='frontend_listing_box_article_unit_reference_content'}
				<span class="is--nowrap">
					({$sArticle.lenzVariantsEverywhereVariantReferencePrice|currency}
					{s name="Star"}{/s} / {$sArticle.lenzVariantsEverywhereVariantReferenceUnit} {$sArticle.lenzVariantsEverywhereVariantUnit})
				</span>
			{/block}
		{/if}
	</div>
{else}
	{* default implementation *}
	<div class="price--unit">

		{* Price is based on the purchase unit *}
		{if $sArticle.purchaseunit && $sArticle.purchaseunit != 0}

			{* Unit price label *}
			{block name='frontend_listing_box_article_unit_label'}
				<span class="price--label label--purchase-unit is--bold is--nowrap">
					{s name="ListingBoxArticleContent"}{/s}
				</span>
			{/block}

			{* Unit price content *}
			{block name='frontend_listing_box_article_unit_content'}
				<span class="is--nowrap">
					{$sArticle.purchaseunit} {$sArticle.sUnit.description}
				</span>
			{/block}
		{/if}

		{* Unit price is based on a reference unit *}
		{if $sArticle.purchaseunit && $sArticle.purchaseunit != $sArticle.referenceunit}

			{* Reference unit price content *}
			{block name='frontend_listing_box_article_unit_reference_content'}
				<span class="is--nowrap">
					({$sArticle.referenceprice|currency}
					{s name="Star"}{/s} / {$sArticle.referenceunit} {$sArticle.sUnit.description})
				</span>
			{/block}
		{/if}
	</div>
{/if}
