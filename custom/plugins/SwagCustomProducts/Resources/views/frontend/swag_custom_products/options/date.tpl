{namespace name="frontend/detail/option"}

{* DateField *}
{block name="frontend_detail_swag_custom_products_options_datefield_field"}
    <input class="wizard--input" type="text" name="custom-option-id--{$option['id']}"
           id="custom-products-option-{$key}"
           data-datepicker="true"
           data-field="true"
            {if $option['min_date']} data-minDate="{$option['min_date']}"{/if}
            {if $option['max_date']} data-maxDate="{$option['max_date']}"{/if}
            {if $option['default_value']} data-defaultDate="{$option['default_value']}"{/if}
            {if $option['placeholder']} placeholder="{$option['placeholder']}"{/if}
            {if $option['required']} data-validate="true" data-validate-message="{s name='detail/validate/date'}{/s}"{/if}
    />
{/block}