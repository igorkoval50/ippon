{* Instock informations *}
{block name='frontend_widgets_instock_infos'}
    <div class="product--instock">
        {if $sArticle.instock <= 0}
            <link itemprop="availability" href="http://schema.org/LimitedAvailability" />
            <p class="instock--information">
                <span class="instock--text  instock--text-not-available">
                    <i class="instock--status-icon instock--status-not-available"></i>
                    0 {$sArticle.packunit} {s namespace="frontend/plugins/index/instockInformations" name="DetailDataInfoInstockNotAvailable"}verfügbar{/s}
                </span>
            </p>
        {elseif $sArticle.instock <= 5}
            <link itemprop="availability" href="http://schema.org/PreOrder" />
            <p class="instock--information">
                <span class="instock--text instock--text-more-is-coming">
                    <i class="instock--status-icon instock--status-more-is-coming"></i>
                    {$sArticle.instock} {$sArticle.packunit} {s namespace="frontend/plugins/index/instockInformations" name="DetailDataInfoInstockPartialAvailable"}verfügbar{/s}
                </span>
            </p>
        {else}
            <link itemprop="availability" href="http://schema.org/InStock" />
            <p class="instock--information">
                <span class="instock--text instock--text-available">
                    <i class="instock--status-icon instock--status-available"></i>
                    {$sArticle.instock} {$sArticle.packunit} {s namespace="frontend/plugins/index/instockInformations" name="DetailDataInfoInstockAvailable"}verfügbar{/s}
                </span>
            </p>
        {/if}
    </div>
{/block}
