mmFbPixel.events.push({
    ViewManufacturer: {
        name: '{$manufacturer->getName()|escape:'quotes'}',
        id: '{$manufacturer->getId()}',
        content_type: 'product',
        contents: {$mediameetsFacebookPixel.data.manufacturer.contents|json_encode}
    }
});
