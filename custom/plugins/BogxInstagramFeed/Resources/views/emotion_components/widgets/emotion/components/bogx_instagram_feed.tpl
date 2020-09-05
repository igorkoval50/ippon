{*
{$Data|var_dump}
{$item.link}
{$item.url}
*}
{$bogxCellWidth = 100.0/({$Data['items-in-row']}|intval)}
<div class="bogx--instagram-feed panel" id="bogx_instagram_data"
	 data-username="{$Data['username']}"
	 data-limit="{$Data['limit']}"
	 data-in_row="{$Data['items-in-row']}"
	 data-cell_width="{$bogxCellWidth}"
	 data-padding_x="{$Data['horizontal_padding']}"
	 data-padding_y="{$Data['vertical_padding']}"
	 data-cache_suffix="{$Data['cache_suffix']}"
	 data-layout="{$Data['layout']}"
	 data-profile="{$Data['profile']}"
	 data-captions="{$Data['captions']}"
	 data-counts="{$Data['counts']}">
	{* debug *}
	{* $Data|@var_dump *}
    <ul class="bogx--flexbox-justifiy" id="bogx_instagram"></ul>
	<script type="text/javascript">

		(function(){
			new InstagramFeed({
				'username': "{$Data['username']}",
				'container': document.getElementById("bogx_instagram"),
				'display_profile': false,
				'display_gallery': true,
				'items': {$Data['limit']},
				'items_per_row': {$Data['items-in-row']},
				'margin': 0
			});
		})();


		var callback_tosrus = function(){
			// Handler when the DOM is fully loaded
			check_jQ_tosrus();
		};

		if (
				document.readyState === "complete" ||
				(document.readyState !== "loading" && !document.documentElement.doScroll)
		) {
			callback_tosrus();
		} else {
			document.addEventListener("DOMContentLoaded", callback_tosrus);
		}

		function check_jQ_tosrus(){
			if (window.jQuery){
				start_tosrus();
			}
			else{
				window.setTimeout( "check_jQ_tosrus();", 100);
			}
		}

		function start_tosrus() {
			$("#bogx_instagram a").tosrus({
				//	default options (for both desktop and touch-devices)
				keys: { close:true },
				//buttons: { close: $(".tos-slide") },
			});
		}
	</script>		
	{*
	<script async type="text/javascript">
		document.asyncReady(function() {
			$("#bogx-instagram a").tosrus();
		});
	</script>
	*}	
</div>
