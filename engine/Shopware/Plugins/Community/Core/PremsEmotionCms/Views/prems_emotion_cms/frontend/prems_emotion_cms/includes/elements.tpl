{if $emotions}
  <div class="content listing--content">

    <div class="content--emotions">

      {$showListing = false}
      {$fullscreen = false}

      {foreach $emotions as $emotion}
        {if $emotion.position == $position}
          {if $hasEscapedFragment}
            {if 0|in_array:$emotion.devicesArray}
              {if $emotion.showListing == 1}
                {$showListing = true}
              {/if}

              {if $emotion.fullscreen == 1}
                {$fullscreen = 0}
              {/if}

              <div class="emotion--fragment">
                {action module=widgets controller=emotion action=index emotionId=$emotion.id controllerName=$Controller noFullscreen=1}
              </div>
            {/if}
          {else}
            {if $emotion.showListing == 1}
              {$showListing = true}
            {/if}

            {if $emotion.fullscreen == 1}
              {$fullscreen = 0}
            {/if}

            <div class="emotion--wrapper{if $PremsEmotionCmsNoAjax} emotion--no-ajax{/if}"
                 data-controllerUrl="{url module=widgets controller=emotion action=index noFullscreen=1 emotionId=$emotion.id controllerName=$Controller}"
                 data-availableDevices="{$emotion.devices}"
                 data-showListing="{if $emotion.showListing == 1}true{else}false{/if}">
            </div>
          {/if}
        {/if}
      {/foreach}
    </div>
  </div>
<div class="emotions--cls"></div>
{/if}