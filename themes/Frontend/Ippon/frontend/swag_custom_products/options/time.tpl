{namespace name="frontend/detail/option"}

{* Field *}
{block name="frontend_detail_swag_custom_products_options_timefield_field"}
    <input class="wizard--input" type="text" name="custom-option-id--{$option['id']}"
           id="custom-products-option-{$key}"
           data-datepicker="true"
           data-field="true"
           data-enableTime="true"
           data-noCalendar="true"
           data-altFormat="H:i"
           data-altTimeFormat=""
           data-dateFormat="H:i"
           data-timeFormat=""
            {if $option['min_date']} data-minDate="{$option['min_date']|date_format:"H:i"}"{/if}
            {if $option['max_date']} data-maxDate="{$option['max_date']|date_format:"H:i"}"{/if}
            {if $option['default_value']} data-defaultDate="{$option['default_value']|date_format:"H:i"}"{/if}
            {if $option['default_value']} value="{$option['default_value']|date_format:"H:i"}"{/if}
            {if $option['placeholder']} placeholder="{$option['placeholder']}"{/if}
            {if $option['required']} data-validate="true" data-validate-message="{s name='detail/validate/time'}{/s}"{/if}
    />
{/block}