{extends file="parent:frontend/listing/product-box/box-emotion.tpl"}

{* Product name *}
{block name='frontend_listing_box_article_name'}
	{if {controllerName|lower} != 'search' || ({config namespace="LenzVariantsEverywhere" name="showVariantsInSearch" default=true} && {controllerName|lower} == 'search')}
		{if $sArticle.lenz_variants_everywhere_variantname|trim|strlen > 0 && {config name="appendVariantNameToArticleName" namespace="LenzVariantsEverywhere"}}
			{$lenzVariantsEveryWhereTitle = $sArticle['articleName']|cat:' - '|cat:$sArticle['lenz_variants_everywhere_variantname']}
		{elseif $sArticle.additionaltext|trim|strlen > 0 && {config name="appendVariantNameToArticleName" namespace="LenzVariantsEverywhere"} && ($sArticle.lenz_variants_everywhere_show == 1 || {config namespace="LenzVariantsEverywhere" name="showOnlySpecifiedVariants"} == false)}
			{$lenzVariantsEveryWhereTitle = $sArticle['articleName']|cat:' - '|cat:$sArticle['additionaltext']}
		{else}
			{$lenzVariantsEveryWhereTitle = $sArticle.articleName}
		{/if}

		<a href="{$sArticle.linkDetails}"
		   class="product--title"
		   title="{$lenzVariantsEveryWhereTitle|escapeHtml}">
			{$lenzVariantsEveryWhereTitle}
		</a>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
