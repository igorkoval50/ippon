{extends file='parent:frontend/blog/listing.tpl'}

{block name='frontend_blog_listing_filter_button'}
  {* Emotion worlds *}
  {block name="frontend_listing_list_promotion"}
    {if $hasEmotion}
      {$fullscreen = false}

      {block name="frontend_listing_emotions"}
        <div class="content--emotions">

          {foreach $emotions as $emotion}
            {$fullscreen = false}

            <div class="emotion--wrapper{if $PremsEmotionCmsNoAjax} emotion--no-ajax{/if}"
                 data-controllerUrl="{url module=widgets controller=emotion action=index emotionId=$emotion.id controllerName=$Controller}"
                 data-availableDevices="{$emotion.devices}">
            </div>
          {/foreach}
        </div>
      {/block}
    {/if}
  {/block}
  {$smarty.block.parent}
{/block}