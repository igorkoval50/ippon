<form method="post">
<input type="hidden" name="tab" value="backups">
<table class="table table-hover table-striped table-borderless">
	<thead>
		<tr style="border: 0">
			<th>Backup-Name</th>
			<th>Informationen</th>
			<th>Funktionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach $backups as $backup}
		<tr>
		<td>
			<h3>
				<a href="{$basepath}/backend/netzpstaging/download?f={$backup.filename}" 
					class="badge badge-light btn-block">
					{$backup.filename}
				</a>
			</h3>
		</td>
		<td>
			<span class="badge badge-pill badge-info">
				{$backup.filedate|date_format:"d.m.Y H:i"}
			</span>
			<span class="badge badge-pill badge-success">
				{$backup.filesize}
			</span>
		</td>
		<td>
			<a href="{$basepath}/backend/netzpstaging/download?f={$backup.filename}" class="btn btn-success btn-sm">
				Download
			</a>
			<button name="cmd" value="delete_backup.{$backup.filename}" 
					onclick="return confirmDeleteBackup();"
			   		class="btn btn-danger btn-sm">
				Löschen
			</button>
		</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<br>
{if $firstProfile.dbconfig != null}
<a class="btn btn-primary btn-sm" href="#" onclick="start({$firstProfile.id}, 4); return false;">
	Neues Datenbank-Backup erzeugen
</a>
{else}
<span class="badge badge-warning">Bitte richten Sie zunächst ein Profil für eine Testumgebung ein.</span>
{/if}

</form>
