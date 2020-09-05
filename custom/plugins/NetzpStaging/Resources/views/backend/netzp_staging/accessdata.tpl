<h4>
	Zugangsdaten erstellen und verschicken
	<span class="badge badge-warning">{$profile.title}</span>
</h4>
Hinweis: Sie können im Backend der Testumgebung unter <i>Einstellungen / Benutzerverwaltung</i> die eingerichteten Nutzer einsehen und wieder löschen.

<br><br>
<form method="post" onsubmit="return submitForm();">
	<input type="hidden" name="id" value="{$profile.id}" value="{$profile.id}">
	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label>
		    		Zugangsdaten:
		    	</label>
		    </div>
		</div>
    	<div class="col-lg-4">	
			<div class="form-group">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="backend" name="backend"
						   value="1">
					<label class="form-check-label" for="backend">
						Shopware-Backendnutzer erstellen
			    		<a href="#" data-toggle="tooltip" title="Es wird ein Backend-Nutzer eingerichtet, der sich aus 'support' und dem eingegebenen Firmennamen zusammensetzt und der Gruppe 'local_admins' angehört."><i class="fa fa-question-circle"></i></a>
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="files" name="files"
						   value="1">
					<label class="form-check-label" for="files">
						Zugriff auf Dateimanager gewähren
			    		<a href="#" data-toggle="tooltip" title="Es wird ein Nutzer eingerichtet, der Zugriff auf den Dateimanager (/filemanager.php) hat. Dieser Dateimanager steht nur auf der Testumgebung zur Verfügung!"><i class="fa fa-question-circle"></i></a>
					</label>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
	    		Empfänger:
		    </div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="email">
		    		E-Mail:
		    		<a href="#" data-toggle="tooltip" title="Die Zugangsdaten werden an diese Adresse verschickt, Sie erhalten eine Kopie der E-Mail an die Shopbetreiber-Adresse. Der Empfänger hat damit nur Zugriff auf die erzeugte Testumgebung! Prüfen Sie die E-Mailadresse dennoch gewissenhaft."><i class="fa fa-question-circle"></i></a>
		    	</label>
		    	<input type="email" required="required" class="form-control" id="email" name="email">
		  	</div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="name">
		    		Firma:
		    		<a href="#" data-toggle="tooltip" title="Die hier eingegebene Firma wird für die Erzeugung des Backend-Nutzers verwendet."><i class="fa fa-question-circle"></i></a>
		    	</label>
		    	<input type="text" required="required" class="form-control" id="name" name="name">
		  	</div>
		</div>
	</div>

	<br>
	<button class="btn btn-success btn-sm" name="cmd" value="save_accessdata">
		Erstellen und versenden
	</button>
	<button class="btn btn-info btn-sm" name="cmd" value="cancel" formnovalidate>Abbrechen</button>
</form>
