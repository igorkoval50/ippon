{extends file="parent:widgets/emotion/components/component_blog.tpl"}


{block name="widget_emotion_component_blog_entry_image"}
    <div class="entry--wrapper">
        {$smarty.block.parent}

{/block}

{block name="widget_emotion_component_blog_entry_title"}
    <a class="blog--title"
       href="{url controller=blog action=detail sCategory=$entry.categoryId blogArticle=$entry.id}"
       title="{$entry.title|escape}">
       {$entry.title|truncate:80}
    </a>
{/block}

{block name="widget_emotion_component_blog_entry_description"}
        <div class="blog--description">
            {if $entry.shortDescription}
                {$entry.shortDescription|truncate:160}
            {else}
                {$entry.description|strip_tags|truncate:160}
            {/if}
            <a href="{url controller=blog action=detail sCategory=$entry.categoryId blogArticle=$entry.id}" class="blog--readmore" title="{s name='EmotionBlogReadMoreTitle'}{$entry.title|escape|truncate:40:'...':true} weiterlesen{/s}">{s name='EmotionBlogReadMore'}weiterlesen{/s}</a>
        </div>
    </div>
{/block}
                            