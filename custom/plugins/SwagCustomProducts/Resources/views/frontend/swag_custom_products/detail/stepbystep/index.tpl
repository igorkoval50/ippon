{namespace name="frontend/detail/option"}

{block name="frontend_detail_swag_custom_products_wizard"}

    {* the button to open the modal with the wizard *}
    {block name="frontend_detail_swag_custom_products_wizard_open_button"}
        <div class="custom-products--actions">
            <a href="#" class="btn is--large custom-products--open-wizard" data-title="{$swagCustomProductsTemplate['display_name']}" title="{s name="detail/index/btn/configure"}Configure now{/s}">
                {s name="detail/index/btn/configure"}Configure now{/s}
            </a>
        </div>
    {/block}

    {* the wizard content *}
    {block name="frontend_detail_swag_custom_products_wizard_modal_content"}
        <div class="custom-products--wizard-container is--hidden">

        {block name="frontend_detail_swag_custom_products_wizard_modal_content_option_allocation"}
            {$options = $swagCustomProductsTemplate['options']}
        {/block}

        {block name="frontend_detail_swag_custom_products_wizard_modal_content_content"}
            <div class="wizard-container--content">

            {block name="frontend_detail_swag_custom_products_wizard_modal_content_options"}
                <div class="wizard-container--options">
                    {foreach $options as $option}
                        {$path="frontend/swag_custom_products/options/{$option['type']}.tpl"}

                        {if $path|template_exists}
                            <div class="custom-products--option is--wizard
                                {if $option@first} is--first is--active{else} is--hidden{/if}
                                {if $option@last} is--last{/if}"
                                {if $option['required']} data-swag-custom-products-required="true"{/if}>

                                {$optionPercentage = "{if $option['prices'][0]['is_percentage_surcharge']}{$option['prices'][0]['percentage']}%{/if}"}
                                {$addSurcharge = ""}

                                {* Once price for the option *}
                                {block name="frontend_detail_swag_custom_products_options_once_price"}
                                    {if $option['is_once_surcharge']}
                                        {$addSurcharge = "(+ {if $optionPercentage}{$optionPercentage}{else}{$option['surcharge']|currency}{/if}&nbsp;{s name="detail/option/once_price"}{/s}{s name="Star" namespace="frontend/listing/box_article"}{/s})"}
                                    {else}
                                        {* Surcharge price for the option *}
                                        {block name="frontend_detail_swag_custom_products_options_surcharges_price"}
                                            {if $option['surcharge']}
                                                {$packUnit = $sArticle.packunit}

                                                {if !$packUnit}
                                                    {$packUnit="{s name='detail/index/surcharge_price_unit'}{/s}"}
                                                {/if}

                                                {$addSurcharge = "(+ {if $optionPercentage}{$optionPercentage}{else}{$option['surcharge']|currency}{/if} / {$packUnit}{s name="Star" namespace="frontend/listing/box_article"}{/s})"}
                                            {/if}
                                        {/block}
                                    {/if}
                                {/block}

                                <h5>{$option['name']}{if $option['required']}&nbsp;**{/if}<small>&nbsp;{$addSurcharge}</small></h5>
                                {block name="frontend_detail_swag_custom_products_wizard_options_description"}
                                    {if $option['description']}
                                        <div class="option--description">
                                            {$option['description']}
                                        </div>
                                    {/if}
                                {/block}

                                {* Include the actual option template file *}
                                <div class="custom-products--{$option['type']|lower} custom-product--option-wrapper-wizard">
                                    {include file=$path key=$option@index}

                                    {* Option actions *}
                                    {block name="frontend_detail_swag_custom_products_options_actions"}
                                        <div class="custom-product--interactive-bar">
                                            <div class="custom-products--option-actions">

                                                {block name="frontend_detail_swag_custom_products_options_reset_action"}
                                                    <span class="custom-products--option-reset filter--active"
                                                          data-custom-products-reset="true">
                                                        <span class="filter--active-icon"></span>
                                                        {s name="detail/index/reset_values"}reset{/s}
                                                    </span>
                                                {/block}
                                            </div>
                                        </div>
                                    {/block}
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            {/block}

            {* surcharges *}
            {block name="frontend_detail_swag_custom_products_wizard_modal_content_surcharges"}
                <div class="wizard-container--surcharges">

                    {block name="frontend_detail_swag_custom_products_wizard_modal_content_surcharges_container"}
                        <div class="custom-products--global-calculation-overview">
                            {include file="frontend/swag_custom_products/detail/surcharges.tpl"}
                        </div>
                    {/block}
                </div>
            {/block}
            </div>
        {/block}

        {* the navigation bar *}
        {block name="frontend_detail_swag_custom_products_wizard_modal_content_navigation"}
            <div class="wizard-container--navigation">
                <div class="navigation--container">
                    {block name="frontend_detail_swag_custom_products_wizard_modal_content_navigation_back"}
                        <button type="button" class="btn custom-products-navigation--btn-left is--icon-left">
                            <i class="icon--arrow-left"></i> {s name="detail/index/wizard/navigation/back"}Back{/s}
                        </button>
                    {/block}

                    {block name="frontend_detail_swag_custom_products_wizard_modal_content_navigation_next"}
                        <button type="button" class="btn custom-products-navigation--btn-right is--icon-right">
                            {s name="detail/index/wizard/navigation/next"}Next{/s} <i class="icon--arrow-right"></i>
                        </button>
                    {/block}

                    {block name="frontend_detail_swag_custom_products_wizard_modal_content_navigation_select"}
                        <div class="select-field wizard--select-container">
                            <select class="navigation--select" data-compatibility="false">
                                {foreach $options as $option}
                                    <option value="{$option@index}">{$option['name']}{if $option['required']}&nbsp;**{/if}</option>
                                {/foreach}
                            </select>
                        </div>
                    {/block}
                </div>
                {block name="frontend_detail_swag_custom_products_wizard_modal_content_take_configuratin_button"}
                    <div class="wizard-container--take-configuration">
                        <button type="button" class="btn is--primary is--icon-right take-configuration">
                            {s name="detail/index/wizard/assume/configuration"}Assume configuration{/s} <i class="icon--arrow-right"></i>
                        </button>
                    </div>
                {/block}
            </div>
        {/block}

        {block name="frontend_detail_swag_custom_products_wizard_modal_content_required"}
            <div class="wizard-container--required-label">
                <small>** {s name="detail/index/wizard/required/field"}Required field{/s}</small>
            </div>
        {/block}

        </div>
    {/block}
{/block}