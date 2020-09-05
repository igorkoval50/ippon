{extends file="parent:frontend/index/sidebar.tpl"}

{block name="frontend_index_left_menu"}
    {block name="frontend_index_left_menu_individualpromotionbanner"}
        {$controllerName = {$controller_name|lower}}
        {$actionName = {$action_name|lower}}
        {$targetName = {$sTarget|escapeHtml}} 

        {block name="frontend_index_left_menu_individualpromotionbanner_position3"}
            {action module=widgets controller=Promotionbanner action=index position=3 controllerName=$controllerName actionName=$actionName targetName=$targetName}
        {/block}
        {$smarty.block.parent}
    {/block}
{/block}