{namespace name="frontend/detail/option"}

{block name="frontend_detail_swag_custom_products_options_colorselection_fields"}
    <div id="custom-products-option-{$key}" class="wizard--input custom-products--color-selection-wrapper block-group" data-group-field="true"{if $option['required']} data-validate-group="true" data-validate-message="{s name='detail/validate/color_selection'}{/s}"{/if}>
        {foreach $option['values'] as $value}
            {block name="frontend_detail_swag_custom_products_options_colorselection_element"}
                <div class="custom-products--color-selection block">

                    {block name="frontend_detail_swag_custom_products_options_colorselection_hidden_input"}
                        <input type="radio" id="custom-products--color-selection-{$key}-{$value@index}"
                               class="is--hidden"
                               name="custom-option-id--{$option['id']}"
                               value="{$value['id']}"
                            {if $value['is_default_value']}checked="checked" data-default-value="{$value['id']}"{/if}
                            {if $value['required']} required="required"{/if}
                            >
                    {/block}

                    {block name="frontend_detail_swag_custom_products_options_colorselection_label"}
                        <label class="custom-products--color-selection-label" for="custom-products--color-selection-{$key}-{$value@index}">

                            {block name="frontend_detail_swag_custom_products_options_colorselection_color"}
                                <div class="custom-products--color-selection-display" style="background-color: {$value['value']}">
                                    <div class="custom-products--color-selection-overlay">
                                    <!-- Color container -->
                                    </div>
                                </div>
                            {/block}

                            {block name="frontend_detail_swag_custom_products_options_colorselection_description"}
                                <div class="custom-products--color-selection-description">
                                    {$value['name']}
                                </div>
                            {/block}

                            <div class="custom-products--color-selection-price">
                                {block name="frontend_detail_swag_custom_products_options_colorselection_label_surcharge"}
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
