{namespace name="frontend/detail/option"}

{* Index file for the options. This file will load the associated options *}
{block name="frontend_detail_swag_custom_products_options_index"}
    {$customProducts = $swagCustomProductsTemplate}
    {$options = $customProducts['options']}

    {* Options container *}
    {block name="frontend_detail_swag_custom_products_options"}
        {* Do we have to show a notice for required fields? *}
        {$displayRequiredInfo = false}

        {* Terminate if the configuration has a required field *}
        {foreach $options as $option}
            {if $option['required']}
                {$displayRequiredInfo = true}
                {break}
            {/if}
        {/foreach}

        {* Loads the template file by the option name *}
        <div class="custom-products--options">
            <input type="hidden" name="template-id" value="{$customProducts['id']}">

            {foreach $options as $option}
                {$path="frontend/swag_custom_products/options/{$option['type']}.tpl"}

                {if $path|template_exists}
                    <div class="custom-products--option{if $option@first} is--first{/if}{if $option@last} is--last{/if}{if $option['required']} is--active{/if}"{if $option['required']} data-swag-custom-products-required="true"{/if}>

                        {* Label *}
                        {block name="frontend_detail_swag_custom_products_options_label"}
                            <label for="custom-products-option-{$option@index}" class="custom-products--label" data-custom-products-collapse-panel="true" data-label="{$option['name']}">
                                {$option['name']}{if $option['required']}&nbsp;**{/if}

                                {$surcharge = $option['surcharge']}
                                {if $surcharge != 0.00}
                                    {$optionPercentage = "{if $option['prices'][0]['is_percentage_surcharge']}{$option['prices'][0]['percentage']}%{/if}"}

                                    {* Once price for the option *}
                                    {block name="frontend_detail_swag_custom_products_options_once_price"}
                                        {if $option['is_once_surcharge']}
                                            (+ {if $optionPercentage}{$optionPercentage}{else}{$surcharge|currency}{/if}&nbsp;{s name="detail/index/once_price"}{/s}{s name="Star" namespace="frontend/listing/box_article"}{/s})
                                        {/if}
                                    {/block}

                                    {block name="frontend_detail_swag_custom_products_options_surcharges_price"}
                                        {if !$option['is_once_surcharge']}
                                            {$packUnit = $sArticle.packunit}

                                            {if !$packUnit}
                                                {$packUnit= "{s name='detail/index/surcharge_price_unit'}{/s}"}
                                            {/if}

                                            (+ {if $optionPercentage}{$optionPercentage}{else}{$surcharge|currency}{/if} / {$packUnit}{s name="Star" namespace="frontend/listing/box_article"}{/s})
                                        {/if}
                                    {/block}
                                {/if}

                                <a href="#" class="custom-products--toggle-btn">
                                    <i class="icon--arrow-down" data-expanded="icon--arrow-up" data-collapsed="icon--arrow-down"></i>
                                </a>
                            </label>
                        {/block}

                        {* Include the actual option template file *}
                        <div class="custom-product--option-wrapper custom-products--{$option['type']|lower}">
                            {include file=$path key=$option@index}

                            <div class="custom-product--interactive-bar">
                                {* Option actions *}
                                {block name="frontend_detail_swag_custom_products_options_actions"}
                                    <div class="custom-products--option-actions">

                                        {block name="frontend_detail_swag_custom_products_options_reset_action"}
                                            <span class="custom-products--option-reset filter--active" data-custom-products-reset="true">
                                                <span class="filter--active-icon"></span>
                                                {s name="detail/index/reset_values"}reset{/s}
                                            </span>
                                        {/block}
                                    </div>
                                {/block}

                                {* add the description for each option *}
                                {block name="frontend_detail_swag_custom_products_options_description"}
                                    {if $option['description']}
                                        {block name="frontend_detail_swag_custom_products_options_description_link"}
                                            <div class="is--hidden custom-product--modal-content{$option['id']}">
                                                {$option['description']}
                                            </div>
                                            <div class="custom-products--option-description-link" data-description-plugin="true"
                                                 data-title="{s name='detail/index/btn/read/description'}More information{/s}"
                                                 data-content-selector="custom-product--modal-content{$option['id']}">

                                                <span>{s name="detail/index/btn/read/description"}More information{/s}&nbsp;</span>
                                                <i class="icon--service"></i>
                                            </div>
                                        {/block}
                                    {/if}
                                {/block}
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}

            {* Global form actions *}
            {block name="frontend_detail_custom_products_actions"}
                <div class="custom-products--option-reset-all">
                    <span class="custom-products--global-reset filter--active">
                        <span class="filter--active-icon"></span>
                        {s name="detail/index/reset_configuration"}Reset configuration{/s}
                    </span>
                </div>
            {/block}

            {* Do we have to show a notice for required fields? *}
            {block name="frontend_detail_custom_products_required_info"}
                {if $displayRequiredInfo}
                    <div class="custom-products--required-field-info">
                        **&nbsp;{s name="detail/option/index/required_field_info"}Required fields{/s}
                    </div>
                {/if}
            {/block}
        </div>
    {/block}
{/block}
