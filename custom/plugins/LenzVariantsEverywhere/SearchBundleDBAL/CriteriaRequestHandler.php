<?php

namespace LenzVariantsEverywhere\SearchBundleDBAL;

use Enlight_Controller_Request_RequestHttp as Request;
use LenzVariantsEverywhere\SearchBundleDBAL\Condition\ShowVariantsCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaRequestHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class CriteriaRequestHandler implements CriteriaRequestHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handleRequest(
        Request $request,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        if (!Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'show')) {
            // abort if shop is not activated
            return;
        }

        if($request->getParam('var', null) !== null) {
            return;
        }

        // Exclude search via option.
        if(
            strtolower($request->getControllerName()) == 'listing'
            || (strtolower($request->getControllerName()) == 'search' && Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'showVariantsInSearch', true))
            || (strtolower($request->getControllerName()) == 'ajax_search'  && Shopware()->Config()->getByNamespace('LenzVariantsEverywhere', 'showVariantsInSearch', true))
            // Support for productNavigation.
            && !($request->getModuleName() == 'widgets' && $request->getControllerName() == 'listing' && $request->getActionName() == 'productNavigation')
        ) {
            $criteria->addCondition(
                new ShowVariantsCondition(

                )
            );
        }
    }
}
