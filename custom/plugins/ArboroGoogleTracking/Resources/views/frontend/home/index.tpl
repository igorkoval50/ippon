{extends file="parent:frontend/home/index.tpl"}

{block name='frontend_arboro_tracking'}
    {if $optimizeCID && 'home'|in_array:$optimizeDisplayConfig}
        {include file='frontend/index/optimize.tpl'}
    {/if}

    {$smarty.block.parent}
{/block}


{*
{block name='frontend_arboro_tracking'}

    {$smarty.block.parent}

    {if $conversionID}
        {if $enableRemarketing}
            {block name='arboro_tracking_home_remarketing'}
                <script type="text/javascript">
                    {literal}
                    var google_tag_params = {
                        ecomm_pagetype: "home"
                    };
                    {/literal}
                </script>

                {block name='arboro_tracking_home_remarketing_tag'}
                    {include file='frontend/remarketing_tag.tpl'}
                {/block}
            {/block}
        {/if}
    {/if}
{/block}
*}
