{extends file="parent:widgets/emotion/components/component_banner.tpl"}

{block name="widget_emotion_component_banner_inner"}
    {$smarty.block.parent}

    {if $Data.title}<div class="banner--title">{$Data.title}</div>{/if}
{/block}