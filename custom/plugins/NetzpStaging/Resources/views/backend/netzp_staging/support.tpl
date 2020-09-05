<div class="card-deck">
  <div class="card bg-light">
    <div class="card-body text-center">
      <p class="card-text">
        <h5>Plugin Dokumentation<br>&nbsp;</h5>
        <br>
        <a class="btn btn-primary" href="https://plugins.netzperfekt.de/doc/testserver-2" target="_blank">Dokumentation</a>
      </p>
    </div>
  </div>
  <div class="card bg-light">
    <div class="card-body text-center">
      <p class="card-text">
      	<h5>Fragen und Antworten zum Plugin</h5>
        <br>
      	<a class="btn btn-success" href="https://plugins.netzperfekt.de/faq/Netzp54683877141" target="_blank">FAQ</a>
      </p>
    </div>
  </div>
  <div class="card bg-light">
    <div class="card-body text-center">
      <p class="card-text">
      	<h5>Weiterführender Support<br>&nbsp;</h5>
        <br>
      	<a class="btn btn-warning" href="https://plugins.netzperfekt.de/support/Netzp54683877141" 
      		target="_blank">
      		Support-Formular
      	</a>
      </p>
    </div>
  </div>
  <div class="card bg-light">
    <div class="card-body text-center">
      <p class="card-text">
      	<h5>Auf Twitter informiert bleiben<h5>
        <br>
      	<a class="btn btn-secondary" href="https://twitter.com/netzperfekt" target="_blank">
      		<i class="fa fa-twitter" aria-hidden="true"></i>
      		Folgen Sie uns
      	</a>
      </p>
    </div>
  </div>
  <div class="card bg-light">
    <div class="card-body text-center">
      <p class="card-text">
        <h5>Entdecken Sie unsere <a href="https://plugins.netzperfekt.de" target="_blank">Plugins</a><h5>
        <a href="https://plugins.netzperfekt.de" target="_blank" class="card-text">
          <img src="{link file="backend/_resources/img/netzperfekt.png"}" class="mx-auto d-block">
        </a>
      </p>
    </div>
  </div>
</div>

<br>
<p>
  {if {config name="netzpStagingDebug"} == 1}
  Hier können Sie das aktuelle 
  <a href="{$basepath}/backend/netzpstaging/download?t=log" download>Plugin-Logfile</a> 
  laden.
  {/if}
</p>
<p style="font-size: 80%">
  Sollte sich das Plugin komplett aufgehängt haben, können Sie hier den Status 
  <a href="#" onclick="start(0, 6); return false;">zurücksetzen</a>. 
  <b>Verwenden Sie diese Funktion bitte nur im Notfall.</b>
</p>
