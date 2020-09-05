{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_javascript_jquery_lib"}
    {$smarty.block.parent}

    {if $runNetiLanguageDetector}
        <script type="text/javascript">
          var netiLanguageDetectorAsyncCallback = function () {
            Neti.LanguageDetector.setUrl(
              '{if $smarty.server.HTTPS}{url controller='NetiLanguageDetector' action='index' forceSecure}{else}{url controller='NetiLanguageDetector' action='index'}{/if}'
            ).dispatch();
          };

          {if $theme.asyncJavascriptLoading}
          document.asyncReady(function() {
              $(function() {
                  netiLanguageDetectorAsyncCallback();
              })
          });
          {else}
          $(document).ready(netiLanguageDetectorAsyncCallback);
          {/if}
        </script>
    {/if}
{/block}