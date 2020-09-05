{block name="widgets_digital_publishing_preview"}
    <!DOCTYPE html>
    <head>
        <meta charset="utf-8">

        {{compileLess timestamp={themeTimestamp} output="lessFiles"}}
        {foreach $lessFiles as $stylesheet}
            <link href="{$stylesheet}" media="all" rel="stylesheet" type="text/css" />
        {/foreach}

        {if $theme.additionalCssData}
            {$theme.additionalCssData}
        {/if}
    </head>
    <html>
        <body class="dig-pub--preview">
            {include file="widgets/swag_digital_publishing/index.tpl"}

            <script src="{link file="frontend/_public/vendors/js/jquery/jquery.min.js"}"></script>
            <script src="{link file="frontend/_public/src/js/jquery.plugin-base.js"}"></script>
            <script type="text/javascript">
                {include file="../frontend/js/jquery.cover.js"}
            </script>
        </body>
    </html>
{/block}
