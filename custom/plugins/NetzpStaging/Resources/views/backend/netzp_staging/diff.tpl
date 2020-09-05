<h4>
	Dateivergleich zwischen
	<span class="badge badge-warning">{$profile.title}</span> und dem Live-Shop
</h4>
<small>Aus Zeitgründen werden nur die Verzeichnisbäume für 
<span class="badge badge-info">/themes</span>, 
<span class="badge badge-info">/custom</span> sowie 
<span class="badge badge-info">/engine/Shopware/Plugins</span> verglichen.</small>

<form method="post" onsubmit="return submitForm();">
	<input type="hidden" name="id" value="{$profile.id}" value="{$profile.id}">

	{if $diff|@count == 0}
		<div class="alert alert-success" role="alert">
			Live-Shop und Testumgebung sind <i>identisch</i> in den Verzeichnisbäumen 
			<span class="badge badge-info">/themes/...</span>, 
			<span class="badge badge-info">/custom/...</span> sowie 
			<span class="badge badge-info">/engine/Shopware/Plugins/...</span>.
		</div>

	{else}
	<table class="table table-condensed table-striped table-hover">
		{foreach from=$diff key=dir item=files name=loop}
			{if $smarty.foreach.loop.index % 5 == 0}
			<tr style="background-color: #999">
				<th class="text-center">Ordner / Dateiname</th>
				<th class="text-center" style="width: 175px">Live-Shop</th>
				<th class="text-center" style="width: 175px">
					Test&nbsp;&nbsp;&nbsp;<span class="badge badge-warning">{$profile.title}</span>
				</th>
				<th style="width: 200px">&nbsp;</th>
			</tr>
			{/if}

			<tr>
				<td colspan="4"><strong class="text-success">{$dir}</strong></td>
			</tr>
			{foreach from=$files item=file}
			<tr>
				<td>
					<i class="fa fa-chevron-right" aria-hidden="true"></i>
					{$file.fileBasename}
				</td>

				<td {if $file.status == 1}class="text-center"{/if}>
					{if $file.status == 1}
						<i class="fa fa-check text-primary" aria-hidden="true" data-toggle="tooltip" 
						   title="Diese Datei ist nur auf dem LIVE-Shop vorhanden."></i>
					{elseif $file.status == 3}
						<i class="fa fa-file-o text-primary" aria-hidden="true"></i> <span class="text-primary">{$file.sizeA}</span><br>
						<i class="fa fa-clock-o text-primary" aria-hidden="true"></i> <span class="text-primary">{$file.timeA}</span><br>
					{elseif $file.status == 4}
						<i class="fa fa-file-o" aria-hidden="true"></i> {$file.sizeA}</strong><br>
						<i class="fa fa-clock-o" aria-hidden="true"></i> {$file.timeA}</strong><br>
					{else}
						&nbsp;
					{/if}
				</td>

				<td {if $file.status == 2}class="text-center"{/if}>
					{if $file.status == 2}
						<i class="fa fa-check text-primary" aria-hidden="true" data-toggle="tooltip" 
						   title="Diese Datei ist nur auf der Testmgebung vorhanden."></i>
					{elseif $file.status == 3}
						<i class="fa fa-file-o text-primary" aria-hidden="true"></i> {$file.sizeB}</strong><br>
						<i class="fa fa-clock-o text-primary" aria-hidden="true"></i> {$file.timeB}</strong><br>
					{elseif $file.status == 4}
						<i class="fa fa-file-o" aria-hidden="true"></i></i> <span class="text-primary">{$file.sizeB}</span><br>
						<i class="fa fa-clock-o" aria-hidden="true"></i> <span class="text-primary">{$file.timeB}</span><br>
					{else}
						&nbsp;
					{/if}
				</td>

				<td>
					<button class="btn btn-success" name="cmd" value="difffile.{$file.file|escape:'url'}">
						Vergleichen
					</button>
				</td>
			</tr>
			{/foreach}
		{/foreach}
	</table>
	{/if}

	<br>
	<button class="btn btn-success btn-sm" name="cmd" value="cancel" formnovalidate>Schließen</button>
</form>
