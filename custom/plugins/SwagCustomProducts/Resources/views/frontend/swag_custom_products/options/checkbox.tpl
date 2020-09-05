{namespace name="frontend/detail/option"}

{* Output all fields *}
{block name="frontend_detail_swag_custom_products_options_checkbox_fields"}
    <div id="custom-products-option-{$key}" class="wizard--input custom-products--validation-wrapper" data-group-field="true"{if $option['required']} data-validate-group="true" data-validate-message="{s name='detail/validate/checkbox_group'}{/s}"{/if}>
        {foreach $option['values'] as $value}

            <div class="custom-products--checkbox-value">

                {* Output the actual field *}
                {block name="frontend_detail_swag_custom_products_options_checkbox_field"}
                    <label class="custom-products--checkbox-label" for="custom-products--checkbox-{$key}-{$value@index}">
                        <span class="checkbox">
                            <input type="checkbox" id="custom-products--checkbox-{$key}-{$value@index}"
                                   name="custom-option-id--{$option['id']}[]"
                                   value="{$value['id']}"
                                   {if $value['is_default_value']} data-default-value="{$value['id']}"{/if}
                                   {if $value['is_default_value']} checked="checked"{/if} />

                            <span class="checkbox--state"></span>
                        </span>

                        {* Label value *}
                        {block name="frontend_detail_swag_custom_products_options_checkbox_label_value"}
                            {$value['name']}

                            {block name="frontend_detail_swag_custom_products_options_checkbox_label_value_surcharge"}
                                {include file='frontend/swag_custom_products/options/surcharge/surcharge.tpl'}
                            {/block}
                        {/block}
                    </label>
                {/block}
            </div>
        {/foreach}
    </div>
{/block}
