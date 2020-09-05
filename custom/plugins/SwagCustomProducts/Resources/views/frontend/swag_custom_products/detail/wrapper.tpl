{namespace name="frontend/detail/option"}

{block name="frontend_detail_swag_custom_products_default_option_wrapper"}
    {if $swagCustomProductsTemplate}
        <div class="custom-products--data-wrapper panel has--border">

            <form class="custom-products--form"
                  data-custom-url="{url module="widgets" action="saveConfiguration" controller="SwagCustomProducts" fullPath}"
                  data-swag-custom-products-option-manager="true"
                  data-templateId="{$swagCustomProductsTemplate['id']}"
                  data-overview-url="{url module="widgets" action="overviewCalculation" controller="SwagCustomProducts" fullPath}"
                  data-get-configuration-url="{url module="widgets" action="getConfiguration" controller="SwagCustomProducts" fullPath}"
                  data-overview-articleid="{$sArticle.articleID}"
                  data-overview-number="{$sArticle.ordernumber}"
                  {if $swagCustomProductsTemplate['confirm_input']} data-validate-confirm-input="true"{/if}
                  data-overview-format="{"0.00"|currency}"
                  data-star-snippet="{s name="Star" namespace="frontend/listing/box_article"}{/s}"
                  enctype="multipart/form-data"
            >
                {* Headline *}
                {block name="frontend_detail_custom_products_option_header"}
                    {if $swagCustomProductsTemplate['display_name']}
                        <div class="custom-products--header panel--title is--underline">{$swagCustomProductsTemplate['display_name']}</div>
                    {/if}
                {/block}

                <div class="custom-products--container panel--body is--wide">
                    {* Picture *}
                    {block name="frontend_detail_custom_products_template_picture"}
                        {if !empty($swagCustomProductsTemplate['media'])}
                            {$templateMedia = $swagCustomProductsTemplate['media']}
                            {$thumbnails = $templateMedia['thumbnails']}

                            <div class="custom-products--template-picture">
                                {if !empty($thumbnails)}
                                    {foreach $thumbnails as $image}
                                        {$srcSet = "{if $image@index !== 0}{$srcSet}, {/if}{$image['source']} {$image['maxWidth']}w"}
                                    {/foreach}
                                    <img srcset="{$srcSet}" alt="{$templateMedia['name']|escapeHtml}" itemprop="image" />
                                {else}
                                    {$baseSource = $templateMedia['file']}
                                    <img src="{$baseSource}" alt="{$templateMedia['name']|escapeHtml}" itemprop="image" />
                                {/if}
                            </div>
                        {/if}
                    {/block}

                    {* Descriptions *}
                    {block name="frontend_detail_custom_products_template_description"}
                        {if $swagCustomProductsTemplate['description']}
                            <div class="custom-products--description">
                                {$swagCustomProductsTemplate['description']}
                            </div>
                        {/if}
                    {/block}

                    {* Check for the step by step configurator. *}
                    {if $swagCustomProductsTemplate['step_by_step_configurator']}
                        {include file="frontend/swag_custom_products/detail/stepbystep/index.tpl"}
                    {elseif $swagCustomProductsTemplate['display_name']}
                        {include file="frontend/swag_custom_products/detail/default/index.tpl"}
                    {/if}
                </div>

                {if $swagCustomProductsTemplate['confirm_input']}
                    <div class="panel--footer is--hidden">
                        <label class="custom-products--checkbox-label" for="custom-products--checkbox-confirm-input">
                            <span class="checkbox">
                                <input type="checkbox" id="custom-products--checkbox-confirm-input"
                                       data-validate-message="{s name="detail/index/confirm_input"}{/s}"
                                       data-validate-input="true"
                                />

                                <span class="checkbox--state"></span>
                            </span>

                            <strong>{s name="detail/index/confirm_input"}{/s}</strong>
                        </label>
                    </div>
                {/if}
            </form>
        </div>
    {/if}

    {block name="frontend_detail_custom_products_calculation_overview"}
        <div class="custom-products--global-calculation-overview">
            {* Javascript content here *}
        </div>
    {/block}

    {block name="frontend_detail_custom_products_error_overview"}
        <div class="custom-products--global-error-overview">
            {* Javascript content here *}
        </div>
    {/block}
    {block name="frontend_detail_custom_products_surcharges_tpl"}
        {if !$swagCustomProductsTemplate['step_by_step_configurator']}
            {include file="frontend/swag_custom_products/detail/surcharges.tpl"}
        {/if}
    {/block}
{/block}
