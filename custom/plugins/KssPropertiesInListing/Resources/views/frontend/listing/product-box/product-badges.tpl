{extends file="parent:frontend/listing/product-box/product-badges.tpl"}
{namespace name="frontend/listing/box_article"}
{block name="frontend_listing_box_article_esd"}


    {if $sArticle.attributes.properties_attribute}
        {$sArticleProperties = $sArticle.attributes.properties_attribute->get('productProperties')->getGroups()}
        {capture name="sArticlePropertyLicense"}{s name='sArticlePropertyLicense'}Lizenz{/s}{/capture}
        {foreach $sArticleProperties as $propertyGroup}
        {if $propertyGroup->getName()|lower == $smarty.capture.sArticlePropertyLicense|lower}
            <div class="product--badge badge--license">
                {$propertyOption = $propertyGroup->getOptions()}
                {foreach $propertyOption as $propertyO}
                       {$propertyO->getName()}
                {/foreach}
            </div>
        {/if}
    {/foreach}
    {/if}
    {$smarty.block.parent}
{/block}
