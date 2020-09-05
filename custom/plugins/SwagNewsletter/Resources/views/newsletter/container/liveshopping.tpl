{if $sCampaignContainer.installed}
    <h2 style="border: 1px solid #dfdfdf; color: #000; font-size: 12px; margin: 0px; padding: 5px 10px;">{$sCampaignContainer.data.headline}</h2>
    <table border="0" cellpadding="0" cellspacing="5" style="margin:0;padding:0;font-family:Arial,Helvetica;">
        {foreach $sCampaignContainer.values|array_chunk:2 as $articles}
            {include file="newsletter/container/live_shopping_article.tpl" data=$Data articles=$articles}
        {/foreach}
    </table>
{/if}
