{extends file='parent:frontend/blog/detail.tpl'}

{* Description *}
{block name='frontend_blog_detail_description'}
  {include file="frontend/includes/elements.tpl" position=0}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=1}
{/block}

{block name="frontend_detail_prems_emotion_cms_blog"}
  {$smarty.block.parent}
  {include file="frontend/includes/elements.tpl" position=2}
{/block}