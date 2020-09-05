<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class FrontendListing
 * @package ArboroGoogleTracking\Subscriber
 */
class FrontendListing extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Frontend_Listing';

    /**
     * @param \Enlight_Event_EventArgs $args
     *
     * @throws \InvalidArgumentException
     */
    public function onDispatch($args)
    {
        if(!$this->isLicensed()) {
            return;
        }
        /** @var $action \Enlight_Controller_Action */
        $action = $args->getSubject();

        /** @var \Enlight_View_Default view */
        $this->view = $action->View();

        if($this->shouldTrack('enhancedEcommerce')) {
            $sCategoryContent = $this->view->sCategoryContent;
            $sCategoryInfo = $this->view->sCategoryInfo;
            if(null === $sCategoryInfo && null !== $sCategoryContent && is_array($sCategoryContent) && array_key_exists(
                    'name',
                    $sCategoryContent
                ) && count($sCategoryContent) > 0) {
                $this->assign('sCategoryInfo', $sCategoryContent);
            }

            $this->assign('trackingType', $this->getTrackingType());
            $this->assign(
                'shopCurrency',
                Shopware()
                    ->Shop()
                    ->getCurrency()
                    ->getCurrency()
            );

            if($this->isTagManager()) {
                $this->assign('dataLayerName');
            }

            if($this->getConfigElement('conversionID')) {
                $this->assign(['enableRemarketing', 'conversionID']);

                $this->assign('ecomm_pagetype', 'category');
                $ecomm_prodid = '["' . implode('","', array_keys($this->view->getAssign('sArticles'))) . '"]';
                $this->assign('ecomm_prodid', $ecomm_prodid);
            }

            $this->addTemplateToView('listing/index_extended.tpl');
        }
    }
}
