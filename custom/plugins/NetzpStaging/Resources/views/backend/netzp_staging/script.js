var timer = 0;

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
    monitor({$currentProfileId});
});

function sleep(milliseconds) {
	var start = new Date().getTime();
  	for (var i = 0; i < 1e7; i++) {
    	if ((new Date().getTime() - start) > milliseconds){
      		break;
    	}
  	}
}

function openTestserver(basepath, link, profileId) {

    var ok = statusFiles[profileId] == 9 && statusDb[profileId] == 9;
    if ( ! ok) {
        alert('Die Testumgebung wurde noch nicht vollständig erstellt. Backend und Frontend werden daher noch nicht funktionieren.');
        return;
    }

    window.open(basepath + link, '_blank');
}

function start(profile, type) {
    // type: 1 - komplett erstellen, 2 - nur dateien kopieren, 3 - nur datenbank kopieren
    //       4 - datenbank backup erstellen, 5 - testserver löschen
    var prompt = '';
    if(type == 1) {
        prompt = 'Möchten Sie die Testumgebung jetzt komplett erstellen?';
    }
    else if(type == 2) {
        prompt = 'Möchten Sie die Testumgebung (nur Dateien) jetzt erstellen?';
    }
    else if(type == 3) {
        prompt = 'Möchten Sie die Testumgebung (nur Datenbank) jetzt erstellen?';
    }
    else if(type == 4) {
        prompt = 'Möchten Sie jetzt ein neues Datenbank-Backup des LIVE-Servers erstellen? Dieses wird ALLE Daten des Live-Servers enthalten, unabhängig von den gewählten Anonymisierungs-Einstellungen.';
    }
    else if(type == 5) {
        prompt = 'Möchten Sie die Testumgebung jetzt LÖSCHEN (Datenbank + Dateien)? Das kann nicht rückgängig gemacht werden.';
    }
    else if(type == 6) {
        prompt = 'Möchten Sie den Plugin-Status jetzt ZURÜCKSETZEN?';
    }

    swal({
        title: 'Achtung',
        text: prompt,
        type: 'warning',
        animation: false,
        showCancelButton: true,
        focusCancel: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ja, fortfahren',
        cancelButtonText: 'Abbrechen',
    }).then(function(result) {
        if (result.value) {

            if(type == 1 || type == 2) {
                startTask(profile, 'files', 'files');
            }
            if(type == 1 || type == 3) {
                startTask(profile, 'database', 'database');
            }
            if(type == 4) {
                startTask(profile, 'backup', 'backup');
            }
            if(type == 5) {
                startTask(profile, 'files', 'deleteFiles');
                startTask(profile, 'database', 'deleteDatabase');
            }
            if(type == 6) {
                startTask(0, 'reset', 'reset');
            }
            
            monitor(profile);
        }
    });
}

function abort(profile) {

    swal({
        title: 'Achtung',
        text: 'Möchten Sie die Erzeugung der Testumgebung jetzt abbrechen?',
        type: 'warning',
        animation: false,
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ja, abbrechen',
        cancelButtonText: 'Nein, fortfahren',
    }).then(function(result) {
        if (result.value) {
            $('.commands').show();
            abortTask('files');
            abortTask('database');
        }
    });
}

function abortTask(task) {
    $.ajax({
        type: 'GET',
        url: _basePath + '/backend/NetzpStaging/worker?cmd=abort&task=' + task,
        success: function(response) {
        }
    });
}

function results(profile, task) {
    $.ajax({
        type: 'GET',
        url: _basePath + '/backend/NetzpStaging/worker?cmd=results&profile=' + profile + '&task=' + task,
        success: function(response) {
        }
    });
}

function startTask(profile, task, cmd) {

    var script = _basePath + '/backend/NetzpStaging/' + cmd;

    $.ajax({
        type: 'GET',
        url: _basePath + '/backend/NetzpStaging/worker?cmd=start&profile=' + profile + '&task=' + task,
        success: function(response) {
        }
    });
    sleep(100);

    $.ajax({
        type: 'GET',
        url: script + '?profile=' + profile + '&task=' + task,
        success: function (response) {
        },
    });
    sleep(100);
}

function confirmDelete() {
    return confirm('Möchten Sie das Profil wirklich LÖSCHEN? Das kann nicht rückgängig gemacht werden');
}

function confirmDeleteBackup() {
    return confirm('Möchten Sie diesen Datenbank-Backup wirklich LÖSCHEN? Das kann nicht rückgängig gemacht werden');
}

function updateStatus(profile) {

    $.ajax({
        type: 'GET',
        url: _basePath + '/backend/NetzpStaging/worker?profile=' + profile,
        success: function (json) {
            var progressFiles = json.progress.files;
            var progressDb = json.progress.database;

            var elProgressFiles = $('#progress-files-' + profile);
            var elProgressDb = $('#progress-db-' + profile);
            var elStatusText = $('#statustext-' + profile);
            var elCommands = $('#commands-' + profile);
            var elCancel = $('#cancel-' + profile);

            statusFiles[profile] = 0;
            statusDb[profile] = 0;
            if(progressFiles != null) {
                statusFiles[profile] = getStatus(json.state.files, progressFiles[2]);
            }
            if(progressDb != null) {
                statusDb[profile] = getStatus(json.state.database, progressDb[2]);
            }

            if(json.state.files == 'running') {
                $('.commands').hide();
                $('.progress1').hide();
                $('#progress1-' + profile).show();

                elProgressFiles.html(progressFiles[1]);
                if(progressFiles[0] == -1) {
                    elProgressFiles.addClass('progress-bar-striped progress-bar-animated');
                    elProgressFiles.css('width', '100%');
                }
                else {
                    elProgressFiles.removeClass('progress-bar-striped progress-bar-animated');
                    elProgressFiles.css('width', progressFiles[0]+'%');
                }
                elCancel.show();
                if(progressFiles[2]) {
	                elStatusText.html('Wird gelöscht');
                }
                else {
	                elStatusText.html('Wird erzeugt');
    	        }
            }
            if(json.state.database == 'running') {
                $('.commands').hide();
                $('.progress2').hide();
                $('#progress2-' + profile).show();

                elProgressDb.html(progressDb[1]);
                if(progressDb[0] == -1) {
                    elProgressDb.addClass('progress-bar-striped progress-bar-animated');
                    elProgressDb.css('width', '100%');
                }
                else {
                    elProgressDb.removeClass('progress-bar-striped progress-bar-animated');
                    elProgressDb.css('width', progressDb[0]+'%');
                }
                elCancel.show();
                elCommands.hide();
                if(progressDb[2]) {
	                elStatusText.html('Wird gelöscht');
                }
                else {
	                elStatusText.html('Wird erzeugt');
	            }
            }

            if(json.state.files == 'aborted') {
                elProgressFiles.removeClass('progress-bar-striped progress-bar-animated');
                elProgressFiles.css('width', '100%');
                elProgressFiles.html('abgebrochen');
                elCancel.hide();
            }
            if(json.state.database == 'aborted') {
                elProgressDb.css('width', '100%');
                elProgressDb.html('abgebrochen');
                elCancel.hide();
            }

            if(json.state.files == 'done') {
                elProgressFiles.removeClass('progress-bar-striped progress-bar-animated bg-warning');
                elProgressFiles.addClass('bg-success');
                elProgressFiles.css('width', '100%');
                elProgressFiles.html('Dateien: abgeschlossen');
                if(json.state.database != 'running') {
                    elCancel.hide();
                    elCommands.show();
                }
            }
            if(json.state.database == 'done') {
                elProgressDb.removeClass('bg-warning');
                elProgressDb.addClass('bg-success');
                elProgressDb.css('width', '100%');
                elProgressDb.html('Datenbank: abgeschlossen');
                if(json.state.files != 'running') {
                    elCancel.hide();
                    elCommands.show();
                }
            }

            if((json.state.files == 'aborted' ||
                json.state.files == 'done') &&
               (json.state.database == 'aborted' ||
                json.state.database == 'done')) {
                elCancel.hide();
                elCommands.show();
                $('.commands').show();
                var message = 'Abgeschlossen';
                if(progressFiles[2] && progressDb[2]) {
                    message = 'Gelöscht';
                }
                elStatusText.html(json.state.database == 'done' ? message : 'Abgebrochen');
            }
            else if(json.state.files == 'timeout' ||
                    json.state.database == 'timeout') {
                elCancel.hide();
                elCommands.show();
                $('.commands').show();
                elStatusText.html('Timeout!<br>Bitte max_execution_time erhöhen.');
            }
        },
    });
}

function getStatus(statusString, isDeleting) {

    if(statusString == 'new') {
        return 0;
    }
    else if(statusString == 'running') {
        return 1;
    }
    if(statusString == 'aborted') {
        return 2;
    }
    if(statusString == 'timeout') {
        return 3;
    }
    if(statusString == 'done') {
        if(isDeleting) {
            return 4;
        }
        else {
            return 9;
        }
    }
    return 0;
}

function monitor(profile) {
    if(profile === undefined) {
        return;
    }

    if(timer > 0) {
        clearInterval(timer);
        timer = 0;
	}

    updateStatus(profile);
	timer = setInterval(function() { updateStatus(profile); }, 1000);
}
