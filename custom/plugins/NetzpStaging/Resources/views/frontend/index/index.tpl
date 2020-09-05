{extends file='parent:frontend/index/index.tpl'}

{block name='frontend_index_navigation'}
    {if $netzpIsStagingServer && {config name=netzpStagingShowInfo} == 1}
        <div style="position: fixed; z-index: 16777271; right: 1rem; top: 1rem; background-color: red; color: white; padding: 5px; border-radius: 5px; font-size: 20px">
            Testumgebung<br>
            <div style="background-color: white; color: red; font-weight: bold; margin-top: 5px; padding: 0 3px 0 3px; border-radius: 3px">{$netzpStagingServerName}</div>
        </div>
    {/if}
    {$smarty.block.parent}
{/block}
