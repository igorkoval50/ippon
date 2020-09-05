{extends file='parent:frontend/index/index.tpl'}

{block name='frontend_index_header_javascript'}

{$smarty.block.parent}

{if $active}
	{$zopimsource}
	<script>
		$zopim(function() {

			{if $useremail}
			$zopim.livechat.setName('{$username}');
			$zopim.livechat.setEmail('{$useremail}');

			{/if}

			$zopim.livechat.theme.setColor('{$color}');
			$zopim.livechat.window.setPosition('{$position}');
			$zopim.livechat.button.setPosition('{$position}');
			$zopim.livechat.button.setPositionMobile('{$position}');
			$zopim.livechat.window.setTitle('{$title}');
			$zopim.livechat.badge.setText('{$title}');
			$zopim.livechat.concierge.setName('{$agenttext}');
			$zopim.livechat.concierge.setTitle('{$agenttitle}');
			{if $agentimage}
				$zopim.livechat.concierge.setAvatar('{$agentimage}');
			{/if}
		});
	</script>
{/if}
{/block}