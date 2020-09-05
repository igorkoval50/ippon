{extends file="parent:frontend/listing/manufacturer.tpl"}

{block name="frontend_listing_text"}
  {include file="frontend/includes/elements.tpl" position=0}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=1}
{/block}

{block name="frontend_detail_prems_emotion_cms_supplier"}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=2}
{/block}