{if isset($voteConfirmed) and isset($newsletterIsOptIn) and (($newsletterIsOptIn === true and $voteConfirmed === true) or ($newsletterIsOptIn === false and $voteConfirmed === false)) and isset($sStatus) and $sStatus['code'] == 3 and isset($sUnsubscribe) and $sUnsubscribe === false }
    mmFbPixel.events.push('Lead');
{/if}