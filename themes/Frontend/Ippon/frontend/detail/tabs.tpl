{extends file="parent:frontend/detail/tabs.tpl"}

{block name="frontend_detail_tabs_navigation_inner"}

    {$smarty.block.parent}

    {* Description tab *}
    {block name="frontend_detail_tabs_properties"}
        <a href="#" class="tab--link" title="{s name='DetailPropertiesHeader'}Eigenschaften{/s}" data-tabName="properties">{s name="DetailPropertiesHeader"}Eigenschaften{/s}</a>
    {/block}
{/block}


{block name="frontend_detail_tabs_content_inner"}

    {$smarty.block.parent}

    {* Properties container *}
    {block name="frontend_detail_tabs_content_properties"}
        <div class="tab--container">
            {block name="frontend_detail_tabs_content_properties_inner"}

                {* Properties preview *}
                {block name="frontend_detail_tabs_properties_preview"}
                    <div class="tab--preview">
                        {block name="frontend_detail_tabs_content_properties_preview_inner"}
                            {$sArticle.description_long|strip_tags|truncate:100:'...'}<a href="#" class="tab--link" title="{s name="PreviewTextMore"}{/s}">{s name="PreviewTextMore"}{/s}</a>
                        {/block}
                    </div>
                {/block}

                {* Description content *}
                {block name="frontend_detail_tabs_content_properties_properties"}
                    <div class="tab--content">
                        {block name="frontend_detail_tabs_content_properties_properties_inner"}
                            {include file="frontend/detail/tabs/properties.tpl"}
                        {/block}
                    </div>
                {/block}

            {/block}
        </div>
    {/block}

{/block}