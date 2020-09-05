{namespace name="frontend/detail/option"}

{block name="frontend_detail_swag_custom_products_options_imageselection_fields"}
    {$type = 'radio'}

    {if $option.allows_multiple_selection}
        {$type = "checkbox"}
    {/if}

    <div id="custom-products-option-{$key}" class="wizard--input custom-products--color-selection-wrapper block-group" data-group-field="true"{if $option['required']} data-validate-group="true" data-validate-message="{s name='detail/validate/image_selection'}{/s}"{/if}>
        {foreach $option['values'] as $value}
            {block name="frontend_detail_swag_custom_products_options_imageselection_element"}
                <div class="custom-products--color-selection block">

                    {block name="frontend_detail_swag_custom_products_options_imageselection_hidden_input"}
                        <input type="{$type}" id="custom-products--color-selection-{$key}-{$value@index}"
                               class="is--hidden"
                               name="custom-option-id--{$option['id']}[]"
                               value="{$value['id']}"
                               data-field="true"
                               {if $value['is_default_value']}checked="checked" data-default-value="{$value['id']}"{/if}
                               {if $value['required']} required="required"{/if}
                        >
                    {/block}

                    {block name="frontend_detail_swag_custom_products_options_imageselection_label"}
                        <label class="custom-products--color-selection-label custom-products--image-selection-label" for="custom-products--color-selection-{$key}-{$value@index}">
                            {block name="frontend_detail_swag_custom_products_options_imageselection_color"}
                                <div class="custom-products--color-selection-display custom-products--image-selection-display">
                                    {if $value['image']}
                                        {$valueMedia = $value['image']}
                                        {$thumbnails = $valueMedia['thumbnails']}

                                        {if !empty($thumbnails)}
                                            {foreach $thumbnails as $image}
                                                {$srcSet = "{if $image@index !== 0}{$srcSet}, {/if}{$image['source']} {$image['maxWidth']}w"}
                                            {/foreach}
                                            <img class="color-selection-display--image" srcset="{$srcSet}" alt="{if $value['seo_title']}{$value['seo_title']|escapeHtml}{else}{$valueMedia['name']|escapeHtml}{/if}" itemprop="image" />
                                        {else}
                                            {$baseSource = $valueMedia['file']}
                                            <img class="color-selection-display--image banner--image" src="{$baseSource}" alt="{$valueMedia['name']|escapeHtml}" itemprop="image" />
                                        {/if}

                                        <div class="custom-products--color-selection-overlay">
                                            <!-- image container -->
                                        </div>
                                    {/if}
                                </div>
                            {/block}

                            {block name="frontend_detail_swag_custom_products_options_imageselection_description"}
                                <div class="custom-products--color-selection-description custom-products--image-selection-description">
                                    {$value['name']}
                                </div>
                            {/block}

                            <div class="custom-products--color-selection-price custom-products--image-selection-price">
                                {block name="frontend_detail_swag_custom_products_options_imageselection_label_surcharge"}
                                    {include file='frontend/swag_custom_products/options/surcharge/surcharge.tpl'}
                                {/block}
                            </div>
                        </label>
                    {/block}
                </div>
            {/block}
        {/foreach}
    </div>
{/block}
