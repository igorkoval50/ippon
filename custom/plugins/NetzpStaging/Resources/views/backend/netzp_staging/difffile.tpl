<style>
.difftable {
	margin: 2rem 0 2rem 0;
	border: 1px solid #ccc;
}

.diff {
	background-color: white !important;
}

.diff td {
	vertical-align: top;
	white-space: pre;
	white-space: pre-wrap;
	font-family: monospace;
	text-align: left;
	padding: 5px;
}

.diff th {
	border-right: 1px solid #ccc;
	border-bottom: 2px solid #ccc;
	margin-bottom: 1rem;
	padding: 5px;
	background-color: #ccc !important;
}

.diff td {
	border-right: 1px solid #ccc;
}

.diff td.diffUnmodified {
	text-decoration: none !important;
}

.diff td.diffDeleted {
	text-decoration: line-through !important;
}

.diff td.diffInserted {
	text-decoration: none !important;
	background-color: #FFFF66;
}

tr:nth-child(even) {
	background-color: #f2f2f2;
}
</style>

<h4>
	Dateivergleich zwischen Live-Shop und Testumgebung 
	<span class="badge badge-warning">{$profile.title}</span> der Datei
	<span class="badge badge-info">{$diffFile.filename}</span> 
</h4>


<form method="post">
	<button class="btn btn-success btn-sm" name="cmd" value="diff.{$profile.id}">Schließen</button>
	<div class="difftable">
		{$diffFile.diff}
	</div>
	<button class="btn btn-success btn-sm" name="cmd" value="diff.{$profile.id}">Schließen</button>
</form>