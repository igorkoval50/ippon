{if !$sSupport.sElements && $sSupport.text2}
    mmFbPixel.events.push({
        Contact: {
            content_name: '{$sSupport.name|escape:"quotes"}'
        }
    });
{/if}