{extends file='parent:documents/index.tpl'}

{* we have to create an almost equal template for the delivery note to show custom products options and values *}

{block name="document_index_table_head_tax"}
{/block}

{block name="document_index_table_head_price"}
{/block}

{block name="document_index_table_tax"}
{/block}

{block name="document_index_table_price"}
{/block}

{block name="document_index_amount"}
{/block}

{block name="document_index_head_bottom"}
    <h1>{s name="DocumentIndexShippingNumber"}{/s} {$Document.id}</h1>
    {s name="DocumentIndexPageCounter"}{/s}
{/block}

{block name="document_index_selectAdress"}
    {assign var="address" value="shipping"}
{/block}

{* customize the delivery note to show custom products options and values *}
{* CustomProducts modes: 2 = option, 3 = value *}
{block name="document_index_table_each"}
    {if $position.modus == 0 || $position.modus == 1
    || ($position.modus == 4 && $position.attributes.swag_custom_products_mode === '2')
    || ($position.modus == 4 && $position.attributes.swag_custom_products_mode === '3')}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="document_index_head_right" append}
    {if $Document.bid}{s name="DocumentIndexInvoiceID"}{/s} {$Document.bid}<br/>{/if}
{/block}
