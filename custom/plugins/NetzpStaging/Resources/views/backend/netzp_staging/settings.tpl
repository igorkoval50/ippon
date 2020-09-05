<h4>
	Testumgebung | Einstellungen
	<span class="badge badge-warning">{$profile.title}</span>
</h4>

<br>
<form method="post" id="profileform">
	<input type="hidden" name="id" value="{$profile.id}" value="{$profile.id}">
	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label>
		    		Wartungsmodus:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Bei eingeschaltetem Wartungsmodus ist das Frontend der Testumgebung nicht erreichbar"><i class="fa fa-question-circle"></i></a>
		    	</label>
		    </div>
		</div>
    	<div class="col-lg-4">	
			<div class="form-group">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="maintenance" name="settings[maintenance]"
						   value="1" {if $profile.settings.maintenance}checked="checked"{/if}>
					<label class="form-check-label" for="maintenance">
						Wartungsmodus einschalten
					</label>
				</div>		    	
		  	</div>
		</div>
	</div>

	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="auth">
		    		Verzeichnisschutz:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Sie können hiermit einen zusätzlichen Verzeichnisschutz auf die Testumgebung legen, so dass dieser nicht öffentlich erreichbar ist und zudem von Suchmaschinen nicht indiziert wird. Um diesen zu entfernen, lassen Sie bitte beider Felder leer."><i class="fa fa-question-circle"></i></a>
		    	</label>
		    </div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="auth_user">Benutzer:</label>
		    	<input type="text" class="form-control" id="auth_user" name="settings[auth_user]"
		    		   value="{$profile.settings.auth_user}">
		  	</div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="auth_pass">Kennwort:</label>
		    	<input type="text" class="form-control" id="auth_pass" name="settings[auth_pass]"
		    		   value="{$profile.settings.auth_pass}">
		  	</div>
		</div>
	</div>

	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label>
		    		Fehlerausgaben:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Hierüber können Sie steuern, ob und welche PHP-Fehlermeldungen in der Testumgebung angezeigt werden sollen."><i class="fa fa-question-circle"></i></a>
		    	</label>
		    </div>
		</div>
    	<div class="col-lg-6">
			<div class="form-group">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="errors1" name="settings[errors1]"
						   value="1" {if $profile.settings.errors1}checked="checked"{/if}>
					<label class="form-check-label" for="errors1">
						Fehler nicht abfangen (<i>noErrorHandler</i>)
					</label>
				</div>		    	
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="errors2" name="settings[errors2]"
						   value="1" {if $profile.settings.errors2}checked="checked"{/if}>
					<label class="form-check-label" for="errors2">
						Aussagekräftige Fehlermeldungen anzeigen (<i>showException</i>)
					</label>
				</div>		    	
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="errors3" name="settings[errors3]"
						   value="1" {if $profile.settings.errors3}checked="checked"{/if}>
					<label class="form-check-label" for="errors3">
						PHP-Fehlerhandler verwenden (<i>throwExceptions</i>)
					</label>
				</div>		    	
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="errors4" name="settings[errors4]"
						   value="1" {if $profile.settings.errors4}checked="checked"{/if}>
					<label class="form-check-label" for="errors4">
						PHP Fehleranzeige (<i>display_errors</i>)
					</label>
				</div>		    	
		  	</div>
		</div>
		<div class="col-lg-4">
			<small class="text-info">
				<a href="https://developers.shopware.com/developers-guide/shopware-config/#php-runtime-settings"
				   target="_blank">
					Weitere Informationen
				</a>
			</small>
		</div>
	</div>

	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label>
		    		Caching:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Hierüber können Sie das Caching-Verhalten steuern"><i class="fa fa-question-circle"></i></a>
		    	</label>
		    </div>
		</div>
    	<div class="col-lg-6">
			<div class="form-group">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="caching1" name="settings[caching1]"
						   value="1" {if $profile.settings.caching1}checked="checked"{/if}>
					<label class="form-check-label" for="caching1">
						Template bei jedem Aufruf neu übersetzen (<i>force compile</i>)
					</label>
				</div>		    	
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="caching2" name="settings[caching2]"
						   value="1" {if $profile.settings.caching2}checked="checked"{/if}>
					<label class="form-check-label" for="caching2">
						HTTP-Cache Debug Informationen
					</label>
				</div>		    	
		  	</div>
		</div>
		<div class="col-lg-4">
			<small class="text-info">
				<a href="https://developers.shopware.com/developers-guide/http-cache/#debugging-the-cache"
				   target="_blank">
					Weitere Informationen
				</a>
			</small>
		</div>
	</div>

	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label>
		    		CSRF-Schutz:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Hierüber können Sie die CSRF-Überprüfung (cross site request forgery) steuern"><i class="fa fa-question-circle"></i></a>
		    	</label>
		    </div>
		</div>
    	<div class="col-lg-6">
			<div class="form-group">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="csrf1" name="settings[csrf1]"
						   value="1" {if $profile.settings.csrf1}checked="checked"{/if}>
					<label class="form-check-label" for="csrf1">
						Frontend
					</label>
				</div>		    	
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="csrf2" name="settings[csrf2]"
						   value="1" {if $profile.settings.csrf2}checked="checked"{/if}>
					<label class="form-check-label" for="csrf2">
						Backend
					</label>
				</div>		    	
		  	</div>
		</div>
		<div class="col-lg-4">
			<small class="text-info">
				<a href="https://developers.shopware.com/developers-guide/csrf-protection/" target="_blank">
					Weitere Informationen
				</a>
			</small>
		</div>
	</div>

	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label>
		    		Weitere Einstellungen:
		    	</label>
		    </div>
		</div>
    	<div class="col-lg-6">
			<div class="form-group">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="norobots" name="settings[norobots]"
						   value="1" {if $profile.settings.norobots}checked="checked"{/if}>
					<label class="form-check-label" for="norobots">
						Suchmaschinen von der Indizierung abhalten
			    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Es wird eine robots.txt-Datei erstellt, die Suchmaschinen von der Indizierung abhält. Achtung: diese wird u.U. nicht von allen Suchmaschinen ausgewertet, so dass zusätzlich ein Verzeichnisschutz sinnvoll ist."><i class="fa fa-question-circle"></i></a>

					</label>
				</div>		    	
				<!-- herausgenommen 15.12.2018 - schadet mehr als es nützt
												 die plugins im testserver können durcheinander kommen
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="nocronjobs" name="settings[nocronjobs]"
						   value="1" {if $profile.settings.nocronjobs}checked="checked"{/if}>
					<label class="form-check-label" for="nocronjobs">
						CRONjobs komplett ausschalten
			    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Um keine unerwünschten Prozesse durch die Testumgebung anzusteuern, können die Shopware-Cronjobs vollständig deaktiviert werden."><i class="fa fa-question-circle"></i></a>
					</label>
				</div>		    	
				-->
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="noemails" name="settings[noemails]"
						   value="1" {if $profile.settings.noemails}checked="checked"{/if}>
					<label class="form-check-label" for="noemails">
						Mailversand deaktivieren (Mails werden in Dateien geschrieben)
			    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Falls aktiviert, wird die Testumgebung keine E-Mails versenden. Diese werden jedoch meist im /tmp-Verzeichnis des Servers gespeichert."><i class="fa fa-question-circle"></i></a>
					</label>
				</div>		    	
		  	</div>
		</div>
		<div class="col-lg-4">
			<small class="text-info">
				<a href="https://developers.shopware.com/developers-guide/csrf-protection/" target="_blank">
					Weitere Informationen
				</a>
			</small>
		</div>
	</div>


	<br>
	<button class="btn btn-success btn-sm" name="cmd" value="save_settings">
		Speichern
	</button>
	<button class="btn btn-info btn-sm" name="cmd" value="cancel">Abbrechen</button>
</form>
