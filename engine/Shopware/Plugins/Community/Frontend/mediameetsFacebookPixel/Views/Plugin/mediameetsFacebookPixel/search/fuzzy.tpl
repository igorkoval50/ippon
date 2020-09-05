{if $term !== '' }
    mmFbPixel.events.push({
        Search: {
            search_string: '{$term|escape:'quotes'}'
        }
    });
{/if}
