{namespace name="frontend/detail/option"}

{block name="frontend_detail_swag_custom_products_options_multiselect"}

    {* Label *}
    {block name="frontend_detail_swag_custom_products_options_multiselect_label"}
        <label class="custom-products--radio-label" for="custom-products-option-{$key}">
            {$value['name']}
        </label>
    {/block}

    {* Field *}
    {block name="frontend_detail_swag_custom_products_options_multiselect_field"}
        <select class="wizard--input" id="custom-products-option-{$key}"
            {if $option['required']} data-validate="true" data-validate-message="{s name='detail/validate/multiselect'}{/s}"{/if}
                data-field="true"
                multiple="multiple"
                size="5"
                data-no-fancy-select="true"
                name="custom-option-id--{$option['id']}[]">

            {* Placeholder is a disabled option *}
            {block name="frontend_detail_swag_custom_products_options_multiselect_placeholder"}
                {if $option['placeholder']}
                    <optgroup label="{$option['placeholder']}">
                {/if}
            {/block}

            {* Values *}
            {block name="frontend_detail_swag_custom_products_options_multiselect_values"}
                {foreach $option['values'] as $value}

                    <option value="{$value['id']}"{if $value['is_default_value']} selected="selected"{/if}>
                        {$value['name']}
                        {block name="frontend_detail_swag_custom_products_options_multiselect_values_surcharge"}
                            {include file='frontend/swag_custom_products/options/surcharge/surcharge.tpl'}
                        {/block}
                    </option>
                {/foreach}
            {/block}

            {block name="frontend_detail_swag_custom_products_options_multiselect_placeholder_closing"}
                {if $option['placeholder']}
                    </optgroup>
                {/if}
            {/block}
        </select>
    {/block}
{/block}
