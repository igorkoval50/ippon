<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class FrontendSearch
 * @package ArboroGoogleTracking\Subscriber
 */
class FrontendSearch extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Frontend_Search';

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

        /** @var \Enlight_Controller_Request_Request $request */
        $request = $action->Request();

        /** @var \Enlight_View_Default view */
        $this->view = $action->View();

        if($this->shouldTrack('enhancedEcommerce') && $request->getActionName() === 'defaultSearch') {
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

                $this->assign('ecomm_pagetype', 'searchresults');
            }

            $this->addTemplateToView('search/fuzzy_extended.tpl');
        }
    }
}
