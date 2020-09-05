{namespace name="frontend/detail/tabs/properties"}

{* Offcanvas buttons *}
{block name='frontend_detail_properties_buttons_offcanvas'}
    <div class="buttons--off-canvas">
        {block name='frontend_detail_properties_buttons_offcanvas_inner'}
            <a href="#" title="{"{s name="OffcanvasCloseMenu"}schließen{/s}"|escape}" class="close--off-canvas">
                <i class="icon--arrow-left"></i>
                {s name="OffcanvasCloseMenu"}schließen{/s}
            </a>
        {/block}
    </div>
{/block}

{* Video Content *}
{block name='frontend_detail_tabs_properties_content'}
    <div class="content--description content--properties">

        {* Properties *}
        {block name='frontend_detail_description_properties'}
            {if $sArticle.sProperties}
                <div class="product--properties panel has--border">
                    <table class="product--properties-table">

                        <tr>
                            {* Product SKU - Label *}
                            {block name='frontend_detail_data_ordernumber_label'}
                            <td>
                                <strong class="entry--label">
                                    {s name="DetailDataId" namespace="frontend/detail/data"}{/s}
                                </strong>
                            </td>
                            {/block}

                            {* Product SKU - Content *}
                            {block name='frontend_detail_data_ordernumber_content'}
                            <td>
                                <meta itemprop="productID" content="{$sArticle.articleDetailsID}"/>
                                <span class="entry--content" itemprop="sku">
                                    {$sArticle.ordernumber}
                                </span>
                            </td>
                            {/block}
                        </tr>

                        {foreach $sArticle.sProperties as $sProperty}
                            <tr class="product--properties-row">
                                {* Property label *}
                                {block name='frontend_detail_description_properties_label'}
                                    <td class="product--properties-label is--bold">{$sProperty.name|escape}:</td>
                                {/block}

                                {* Property content *}
                                {block name='frontend_detail_description_properties_content'}
                                    <td class="product--properties-value">{$sProperty.value|escape}</td>
                                {/block}
                            </tr>
                        {/foreach}
                    </table>
                </div>
            {/if}
        {/block}

    </div>
{/block}