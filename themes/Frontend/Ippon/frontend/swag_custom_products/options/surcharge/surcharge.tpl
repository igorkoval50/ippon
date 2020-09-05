{namespace name="frontend/detail/option"}

{block name="frontend_detail_swag_custom_products_values_surcharges"}
    {$valuePercentage = "{if $value['prices'][0]['is_percentage_surcharge']}{$value['prices'][0]['percentage']}%{/if}"}

    {* Once price for the option *}
    {if $value['is_once_surcharge']}
        {block name="frontend_detail_swag_custom_products_values_surcharges_once_price"}
            (+ {if $valuePercentage}{$valuePercentage}{else}{$value['surcharge']|currency}{/if}&nbsp;{s name="detail/option/once_price"}{/s}{s name="Star" namespace="frontend/listing/box_article"}{/s})
        {/block}
    {else}

        {* Surcharge price for the option *}
        {block name="frontend_detail_swag_custom_products_values_surcharges_price"}
            {if $value['surcharge']}
                {$packUnit = $sArticle.packunit}

                {if !$packUnit}
                    {$packUnit="{s name='detail/index/surcharge_price_unit'}{/s}"}
                {/if}

                (+ {if $valuePercentage}{$valuePercentage}{else}{$value['surcharge']|currency}{/if} / {$packUnit}{s name="Star" namespace="frontend/listing/box_article"}{/s})
            {/if}
        {/block}
    {/if}
{/block}
