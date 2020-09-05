{extends file="parent:widgets/emotion/components/component_category_teaser.tpl"}

    {* Category teaser title *}
    {block name="widget_emotion_component_category_teaser_title"}
        <span class="category-teaser--title">
            <span class="teaser-title--inner">
               {$Data.categoryName}
            </span>
        </span>
    {/block}
