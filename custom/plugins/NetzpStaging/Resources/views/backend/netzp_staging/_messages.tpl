<br>
{foreach key=message item=type from=$msg}
	<div class="alert alert-dismissible fade show alert-{if $type == 0}success{elseif $type == 1}warning{elseif $type == 2}danger{/if}">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{if $type == 0}
			<b>OK</b>:
		{elseif $type == 1}
			<b>Wichtig</b>:
		{elseif $type == 2}
			<b>Achtung</b>:
		{/if}
  		{$message}
	</div>
{/foreach}