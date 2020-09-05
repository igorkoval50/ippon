<script>
	var statusFiles = [],
	    statusDb = [];
</script>

<form method="post">
<input type="hidden" name="tab" value="profiles">
<table class="table table-hover table-striped table-borderless">
	<thead>
		<tr>
			<th>Name</th>
			<th>Unterverzeichnis</th>
			<th>Erstellungsdatum</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		{foreach $profiles as $profile}
		<script>
			 statusFiles[{$profile.id}] = {$profile.statusfiles};
			 statusDb[{$profile.id}] = {$profile.statusdb};
		</script>
		<tr>
		<td style="width: 220px">
			<h3><span class="badge badge-primary btn-block">{$profile.title}</span></h3>
			<div style="width: 100%; margin-bottom: 5px">
				<a href="#" onclick="openTestserver('{$basepath}', '{$profile.linkbackend}', {$profile.id}); return false;" 
				   class="btn btn-outline-primary btn-sm">
					<i class="fa fa-gear" aria-hidden="true"></i> Backend
				</a>
				<a href="#" onclick="openTestserver('{$basepath}', '{$profile.linkfrontend}', {$profile.id}); return false;" 
				   class="btn btn-outline-primary btn-sm pull-right"
				   >
					<i class="fa fa-shopping-cart" aria-hidden="true"></i> Frontend
				</a>
			</div>
		</td>
		<td style="width: 200px">
			<span class="badge directory">/{$profile.dirname}</span>
		</td>
		<td style="width: 200px">
			<i class="fa fa-cog" aria-hidden="true" 
				data-toggle="tooltip" title="Optionen der Testumgebung."></i>
			{if $profile.dbconfig.anonymize == "1"}
				<span class="badge badge-pill badge-warning" 
					  data-toggle="tooltip" title="Die Daten der Testumgebung werden anonymisiert.">
					Anonymisiert
				</span>
			{else if $profile.dbconfig.anonymize == "2"}
				<span class="badge badge-pill badge-warning" 
					  style="text-decoration: line-through;"
					  data-toggle="tooltip" title="Es werden keine Kunden/Bestellungen übertragen.">
					Kunden
				</span>
			{/if}
			{if $profile.runfromcron}
				<span class="badge badge-pill badge-warning"
					  data-toggle="tooltip" title="Die Testumgebung wird regelmässig über einen Cron-Job erstellt.">
					Cron
				</span>
			{/if}

			<br>
			<i class="fa fa-folder" aria-hidden="true"></i>
			<span class="badge badge-pill badge-info"
				  data-toggle="tooltip" title="Datum der letzten Erstellung der Dateien">
  				{if $profile.createdfiles}
					{$profile.createdfiles|date_format:"d.m.Y H:i"}
				{else}
					---
				{/if}
			</span>
			<span class="badge badge-pill badge-success"
				  data-toggle="tooltip" title="Anzahl der Erzeugungen der Dateien">
				{$profile.creationsfiles}
			</span>

			<br>
			<i class="fa fa-database" aria-hidden="true"></i>
			<span class="badge badge-pill badge-info"
  				  data-toggle="tooltip" title="Datum der letzten Erstellung der Datenbank">
  				{if $profile.createddb}
					{$profile.createddb|date_format:"d.m.Y H:i"}
				{else}
					---
				{/if}
			</span>
			<span class="badge badge-pill badge-success"
				  data-toggle="tooltip" title="Anzahl der Erzeugungen der Datenbank">
				{$profile.creationsdb}
			</span>
		</td>
		<td style="white-space:nowrap; width: 250px">
			<h5 style="width: 100%">
				<span class="badge badge-{$profile.statuscolor} btn-block"
					  id="statustext-{$profile.id}">
					{$profile.statustext}
				</span>
			</h5>

			<div id="progress1-{$profile.id}" class="progress progress1" 
				 style="display: none; height: 20px; margin-top: 3px">
				<div class="progress-bar bg-warning" id="progress-files-{$profile.id}" 
					 style="width: 0; height: 20px; color: black; background-color: #eee; padding: 2px 3px 2px 3px">
				</div>
			</div>
			<div id="progress2-{$profile.id}" class="progress progress2" 
				 style="display: none; height: 20px; margin-top: 1px">
				<div class="progress-bar bg-warning" id="progress-db-{$profile.id}" 
					 style="width: 0; height: 20px; color: black; padding: 2px 3px 2px 3px">
				</div>
			</div>			
		</td>
		<td class="pull-right">
			<span id="commands-{$profile.id}" class="commands">
				<button name="cmd" value="edit.{$profile.id}" 
						class="btn btn-secondary btn-sm">Bearbeiten</button>
				
				<div class="btn-group">
					<button name="cmd" value="settings.{$profile.id}" 
							class="btn btn-warning btn-sm">Einstellungen</button>
		
					<button class="btn btn-warning btn-sm dropdown-toggle dropdown-toggle-split" 
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu dropdown-menu-right">
						<button name="cmd" value="settings.{$profile.id}" 
								class="dropdown-item" style="cursor: pointer">
							Einstellungen Testumgebung
						</button>
						<button name="cmd" value="accessdata.{$profile.id}" 
								class="dropdown-item" style="cursor: pointer">
							Zugangsdaten verschicken
						</button>
						<button name="cmd" value="diff.{$profile.id}" 
								class="dropdown-item" style="cursor: pointer">
							Dateien vergleichen 
							<i class="fa fa-question-circle"
							data-toggle="tooltip" title="Hinweis: diese Funktion kann zeitaufwändig sein."></i>
						</button>
					</div>
				</div>

				<div class="btn-group">
					<button class="btn btn-dark btn-sm"
							onclick="start({$profile.id}, 1); return false;">
						Erstellen
					</button>
					<button class="btn btn-dark btn-sm dropdown-toggle dropdown-toggle-split" 
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="#" onclick="start({$profile.id}, 1); return false;">
							Erstellen: komplett
						</a>
						<a class="dropdown-item" href="#" onclick="start({$profile.id}, 2); return false;">
							Erstellen: nur Dateien
						</a>
						<a class="dropdown-item" href="#" onclick="start({$profile.id}, 3); return false;">
							Erstellen: nur Datenbank
						</a>
						<a class="dropdown-item" href="#" onclick="start({$profile.id}, 4); return false;">
							Backup Datenbank 
							<i class="fa fa-question-circle"
							data-toggle="tooltip" title="Im Backup sind immer alle Daten enthalten, unabhängig von gewählten Einstellungen für die Anonymisierung."></i>
						</a>
					</div>
				</div>

				<div class="btn-group">
					<a href="#" class="btn btn-danger btn-sm"
						onclick="start({$profile.id}, 5); return false;">
						Löschen
					</a>
					<button class="btn btn-danger btn-sm dropdown-toggle dropdown-toggle-split" 
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu dropdown-menu-right">
						<a href="#" class="dropdown-item" style="cursor: pointer"
							onclick="start({$profile.id}, 5); return false;">
							Testumgebung Löschen
						</a>
						<button name="cmd" value="delete.{$profile.id}" 
								onclick="return confirmDelete();"
								class="dropdown-item" style="cursor: pointer">
							Profil komplett löschen
						</button>
					</div>
				</div>

			</span>
			<a class="btn btn-outline-danger btn-sm" title="Erzeugung abbrechen" style="display: none"
			   id="cancel-{$profile.id}" onclick="abort({$profile.id}); return false;">
				<i class="fa fa-times"></i> Abbrechen
			</a>
		</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<br>
<button class="btn btn-primary btn-sm" name="cmd" value="newprofile">Neue Testumgebung anlegen</button>
<a class="btn btn-info btn-sm" href="https://plugins.netzperfekt.de/doc/testserver-2" target="_blank">Dokumentation</a>

<small class="pull-right">Sie können dieses Fenster während der Erzeugung einer Testumgebung schließen und später wieder zurückkehren.<br><b>Hinweis:</b> Zwischenzeitliche Änderungen im Shop werden unter Umständen nicht übertragen.</small>

</form>
