<h4>
	{if $profile.id == 0}
		Neue Testumgebung anlegen
	{else}
		Testumgebung bearbeiten
	{/if}
</h4>

<form method="post" id="profileform" onsubmit="return submitForm();">
	<input type="hidden" name="id" value="{$profile.id}">
	{if $profile.id > 0}<input type="hidden" name="title" value="{$profile.title}">{/if}
	{if $profile.id > 0}<input type="hidden" name="dirname" value="{$profile.dirname}">{/if}

	<div class="row">
    	<div class="col-lg-3">	
			<div class="form-group">
		    	<label for="title">
		    		Profilname:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Bitte vergeben Sie einen aussagekräftigen Namen für die Testumgebung"><i class="fa fa-question-circle"></i></a>
		    	</label>
		    	<input type="text" class="form-control" id="title" name="title" autofocus="autofocus"
		    		   placeholder="z.B. Testumgebung 1" value="{$profile.title}" 
		    		   {if $profile.id > 0}disabled{/if}>
		  	</div>
		</div>
    	<div class="col-lg-3">	
			<div class="form-group">
		    	<label for="dirname">
		    		Verzeichnis Testumgebung:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="In diesem Unterverzeichnis wird die Testumgebung erzeugt."><i class="fa fa-question-circle"></i></a>
		    	</label>
		    	<input type="text" class="form-control" id="dirname" name="dirname"
		    		   placeholder = "z.b. testserver" value="{$profile.dirname}"
		    		   {if $profile.id > 0}disabled{/if}>
		  	</div>
		</div>
    	<div class="col-lg-3">	
			<div class="form-group">
		    	<label for="dirname">
		    		Anonymisierung:
		    	</label>
				<div class="form-check">
					<input class="form-check-input" type="radio" id="anonymize0" name="dbconfig[anonymize]"
						   value="0" {if $profile.dbconfig.anonymize == null || $profile.dbconfig.anonymize == "0"}checked="checked"{/if}>
					<label class="form-check-label" for="anonymize0">
						Alle Daten übertragen
						<a href="#" tabindex="-1" data-toggle="tooltip" title="Bei der Erzeugung der Datenbank werden alle Daten des LIVE-Shops in die Testumgebung übertragen."><i class="fa fa-question-circle"></i></a>
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" id="anonymize1" name="dbconfig[anonymize]"
						   value="1" {if $profile.dbconfig.anonymize == "1"}checked="checked"{/if}>
					<label class="form-check-label" for="anonymize1">
						Kunden anonymisieren
						<a href="#" tabindex="-1" data-toggle="tooltip" title="Bei der Erzeugung der Datenbank werden folgende Daten anonymisiert: Benutzerdaten, Adressen, E-Mail-Adressen, IP-Adressen, Kommentare, Dokumente"><i class="fa fa-question-circle"></i></a>
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" id="anonymize2" name="dbconfig[anonymize]"
						   value="2" {if $profile.dbconfig.anonymize == "2"}checked="checked"{/if}>
					<label class="form-check-label" for="anonymize2">
						Keine Kunden / Bestellungen
					<a href="#" tabindex="-1" data-toggle="tooltip" title="Bei der Übertragung werden Kundendaten, Bestellungen, Newsletter-Mailings und Dokumente ausgenommen; alle anderen Daten des LIVE-Shops werden übertragen."><i class="fa fa-question-circle"></i></a>
					</label>
				</div>		    	
			</div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="dirname">
		    		Regelmässig erzeugen:
		    		<a href="#" tabindex="-1" data-toggle="tooltip" title="Auf Wunsch kann diese Testumgebung regelmässig über einen Cron-Job erstellt werden."><i class="fa fa-question-circle"></i></a>
		    	</label>
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="runfromcron" name="runfromcron"
						   value="1" {if $profile.runfromcron}checked="checked"{/if}>
					<label class="form-check-label" for="runfromcron">
						über Cron-Job erstellen
					</label>
				</div>		    	
			</div>
		</div>
	</div>

	<br>
	<h5>
		Datenbank
		<a href="#" tabindex="-1" data-toggle="tooltip" title="Sie benötigen eine zusätzliche Datenbank für die Testumgebung"><i class="fa fa-question-circle"></i></a>
	</h5>

	<div class="row">
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="db_host">DB-Server:</label>
		    	<input type="text" class="form-control" id="db_host" name="dbconfig[host]"
		    		   placeholder="localhost" value="{$profile.dbconfig.host}">
		  	</div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="db_port">Port:</label>
		    	<input type="text" class="form-control" id="db_port" name="dbconfig[port]"
		    		   placeholder="3306" value="{$profile.dbconfig.port}">
		  	</div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="db_name">DB-Name:</label>
		    	<input type="text" class="form-control" id="db_name" name="dbconfig[dbname]"
		    		   value="{$profile.dbconfig.dbname}">
		  	</div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="db_user">DB-Benutzer:</label>
		    	<input type="text" class="form-control" id="db_user" name="dbconfig[username]"
		    		   value="{$profile.dbconfig.username}">
		  	</div>
		</div>
    	<div class="col-lg-2">	
			<div class="form-group">
		    	<label for="db_pass">DB-Kennwort:</label>
		    	<input type="text" class="form-control" id="db_pass" name="dbconfig[password]"
		    		   value="{$profile.dbconfig.password}">
		  	</div>
		</div>
	</div>

	<br>
	<h5>
		Ausgeschlossene Verzeichnisse
		<a href="#" tabindex="-1" data-toggle="tooltip" title="Manche Verzeichnisse sind sehr groß, diese können auf Wunsch in der Testumgebung ausgeschlossen werden. Beachten Sie bitte, dass keine systemwichtigen Verzeichnisse ausgeschlossen werden, da Shopware sonst u.U. nicht korrekt funktioniert."><i class="fa fa-question-circle"></i></a>
	</h5>

	<span id='dirs-excluded'></span>
	<div id="tree-excluded"></div>
	
	<br>
	<h5>
		Nur bei Ersterstellung übertragene Verzeichnisse
		<a href="#" tabindex="-1" data-toggle="tooltip" title="Auf Wunsch können einzelne Verzeichnisse nur bei der ersten Erstellung der Testumgebung übertragen werden, um Zeit zu sparen oder eigene Änderungen nicht zu überschreiben."><i class="fa fa-question-circle"></i></a>
	</h5>

	<span id='dirs-notsynced'></span>
	<div id="tree-notsynced"></div>

	<br>
	<button class="btn btn-success btn-sm" name="cmd" value="save">
		{if $profile.id == 0}Jetzt anlegen{else}Speichern{/if}
	</button>
	{if $profile.id == 0}
		<button class="btn btn-info btn-sm" name="cmd" value="cancel">Abbrechen</button>
	{/if}
</form>

<script>
	function submitForm() {
		var dirsExcluded = $('#tree-excluded').jstree('get_selected'),
			dirsNotsynced = $('#tree-notsynced').jstree('get_selected');
		
		$('<input />')
			.attr('type', 'hidden')
			.attr('name', 'dirsexcluded')
			.attr('value', JSON.stringify(dirsExcluded))
          	.appendTo('#profileform');
		$('<input />')
			.attr('type', 'hidden')
			.attr('name', 'dirsnotsynced')
			.attr('value', JSON.stringify(dirsNotsynced))
          	.appendTo('#profileform');

		return true;
	}

	$(function () { 

		$('#tree-excluded')
		.on('changed.jstree', function(event, data) {
    		var selected = [];
    		for(var s in data.selected) {
      			selected.push('<span class="badge badge-warning">' + data.selected[s] + '</span>');
    		}
    		$('#dirs-excluded').html(selected.join(' '));
  		})
  		.on('state_ready.jstree', function(event, data) {
			//$('#tree-excluded').jstree('close_all');
  		})

		.jstree({
			'core' : {
		  		'data' : {
		    		'url' : function(node) {
		    			if(node.id != '#') {
			        		return '{$basepath}/backend/netzpstaging/dirs?basedir=' + 
			        			   encodeURI(node.id);
		    			}
		    			else {
			        		return '{$basepath}/backend/netzpstaging/dirs';
			        	}
		    		},
		    
				    'data' : function(node) {
				      	return { 'id' : node.id };
			      	}
				}
		 	},

		 	'state' : { 'key' : 'netzp_staging_dirsexcluded_{$profile.id}' },
			
			'massload' : {
      			'url' : "{$basepath}/backend/netzpstaging/dirs?id=__dirsexcluded__",
      			'data' : function(nodes) {
        			return { 'ids' : nodes.join(',') };
      			}
			},

			'checkbox': {
				'three_state': false,
				'cascade': 'undetermined'
			},

			'conditionalselect': function(node, event) {
				if(node.state.disabled) {
					alert('Achtung: dieses Verzeichnis können Sie nicht ausschließen, da Shopware sonst nicht korrekt funktioniert.');
					return false;
				}
				return true;
			},
		 	
		 	'plugins' : [ 'state', 'massload', 'checkbox', 'conditionalselect' ]
		});

		$('#tree-notsynced')
		.on('changed.jstree', function(event, data) {
    		var selected = [];
    		for(var s in data.selected) {
      			selected.push('<span class="badge badge-warning">' + data.selected[s] + '</span>');
    		}
    		$('#dirs-notsynced').html(selected.join(' '));
  		})
  		.on('state_ready.jstree', function(event, data) {
			//$('#tree-notsynced').jstree('close_all');
  		})

		.jstree({
			'core' : {
		  		'data' : {
		    		'url' : function(node) {
		    			if(node.id != '#') {
			        		return '{$basepath}/backend/netzpstaging/dirs?basedir=' + 
			        			   encodeURI(node.id);
		    			}
		    			else {
			        		return '{$basepath}/backend/netzpstaging/dirs';
			        	}
		    		},
		    
				    'data' : function(node) {
				      	return { 'id' : node.id };
			      	}
				}
		 	 },

		 	'state' : { 'key' : 'netzp_staging_dirsnotsynced_{$profile.id}' },

			'massload' : {
      			'url' : "{$basepath}/backend/netzpstaging/dirs?id=__dirsnotsynced__",
      			'data' : function(nodes) {
        			return { 'ids' : nodes.join(',') };
      			}
			},

			'checkbox': {
				'three_state': false,
				'cascade': 'undetermined'
			},

			'conditionalselect': function(node, event) {
				return true;
			},
		 	
		 	'plugins' : [ 'state', 'massload', 'checkbox', 'conditionalselect' ]
		});
	});
</script>