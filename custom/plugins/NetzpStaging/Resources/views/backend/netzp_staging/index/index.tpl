{extends file="parent:backend/base/index.tpl"}

{block name="backend/base/container"}
    {if $netzpIsStagingServer && {config name=netzpStagingShowInfo} == 1}
	<div style="position: fixed; z-index: 16777271; right: 5rem; top: 5px; background-color: red; color: white; padding: 5px; border-radius: 5px; font-size: 18px">
	    Testumgebung
	    <span style="background-color: white; color: red; font-weight: bold; padding: 0 3px 0 3px; border-radius: 3px">{$netzpStagingServerName}</span>
	</div>
	{/if}
	{$smarty.block.parent}
{/block}
