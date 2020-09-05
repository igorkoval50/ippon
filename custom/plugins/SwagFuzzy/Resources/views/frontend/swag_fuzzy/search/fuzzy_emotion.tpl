{block name="frontend_search_swag_fuzzy_emotion"}
	<div class="fuzzy--emotion-container">
		{foreach $swagFuzzySynonymGroups as $synonymGroup}
			{if $synonymGroup.normalSearchEmotionId != 0}
				<div class="content--emotions">
					<div class="emotion--wrapper"
						 data-controllerUrl="{url module=widgets controller=emotion action=index emotionId=$synonymGroup.normalSearchEmotionId controllerName=$Controller}"
						 data-availableDevices="{$synonymGroup.normalSearchEmotionDevices}"
						 data-showListing="true">
					</div>
				</div>
			{/if}
		{/foreach}
	</div>
{/block}