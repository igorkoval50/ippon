<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class FrontendDetail
 * @package ArboroGoogleTracking\Subscriber
 */
class FrontendDetail extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Frontend_Detail';

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
            $this->assign('trackingType', $this->getTrackingType());

            if($this->isTagManager()) {
                $this->assign('dataLayerName');
            }

            if($this->getConfigElement('conversionID')) {
                $this->assign(['enableRemarketing', 'conversionID']);

                $this->assign('ecomm_prodid', '"'.$this->view->getAssign('sArticle')['ordernumber'].'"');
                $this->assign('ecomm_totalvalue', $this->view->getAssign('sArticle')['price_numeric']);
                $this->assign('ecomm_pagetype', 'product');
            }

            $this->addTemplateToView('detail/index_extended.tpl');
        }
    }
}
