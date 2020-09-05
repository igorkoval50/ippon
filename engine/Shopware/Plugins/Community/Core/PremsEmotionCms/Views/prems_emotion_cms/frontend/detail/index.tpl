{extends file='parent:frontend/detail/index.tpl'}

{block name="frontend_detail_index_bundle"}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=0}
{/block}


{block name="frontend_detail_index_detail"}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=1}
{/block}

{block name="frontend_detail_prems_emotion_cms_article"}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=2}
{/block}