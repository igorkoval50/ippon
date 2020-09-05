{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_page_wrap"}
    {block name="frontend_index_page_wrap_individualpromotionbanner"}
        {$controllerName = {$controller_name|lower}}
        {$actionName = {$action_name|lower}}
        {$targetName = {$sTarget|escapeHtml}}   

        {block name="frontend_index_page_wrap_individualpromotionbanner_position0"}
            {action module=widgets controller=Promotionbanner action=index position=0 controllerName=$controllerName actionName=$actionName targetName=$targetName}
        {/block}
        {block name="frontend_index_page_wrap_individualpromotionbanner_position2"}
            {action module=widgets controller=Promotionbanner action=index position=2 controllerName=$controllerName actionName=$actionName targetName=$targetName}
        {/block}
        {block name="frontend_index_page_wrap_individualpromotionbanner_position4"}
            {action module=widgets controller=Promotionbanner action=index position=4 controllerName=$controllerName actionName=$actionName targetName=$targetName}
        {/block}
        {$smarty.block.parent}
    {/block}
{/block}

{block name="frontend_index_content_main"}
    {block name="frontend_index_content_main_individualpromotionbanner"}
        {$controllerName = {$controller_name|lower}}
        {$actionName = {$action_name|lower}}
        {$targetName = {$sTarget|escapeHtml}} 

        {block name="frontend_index_content_main_individualpromotionbanner_position1"}
            {action module=widgets controller=Promotionbanner action=index position=1 controllerName=$controllerName actionName=$actionName targetName=$targetName}
        {/block}
        {$smarty.block.parent}
    {/block}
{/block}