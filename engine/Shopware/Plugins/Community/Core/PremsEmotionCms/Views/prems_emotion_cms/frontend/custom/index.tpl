{extends file="parent:frontend/custom/index.tpl"}

{* Custom page tab inner content *}
{block name="frontend_custom_article_content"}
  {include file="frontend/includes/elements.tpl" position=0}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=1}
{/block}

{block name="frontend_detail_prems_emotion_cms_site"}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=2}
{/block}