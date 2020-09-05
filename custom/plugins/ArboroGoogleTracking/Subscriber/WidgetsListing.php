<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class WidgetsListing
 * @package ArboroGoogleTracking\Subscriber
 */
class WidgetsListing extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Widgets_Listing';

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

        $actionName = $request->getActionName();

        if(($actionName === 'ajaxListing' || $actionName === 'top_seller') && $this->shouldTrack(
                'enhancedEcommerce'
            )
        ) {
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

            if($actionName === 'ajaxListing') {
                $this->addTemplateToView('listing/listing_ajax_extended.tpl');
            }

            if($actionName === 'top_seller') {
                $this->addTemplateToView('widgets/top_seller_extended.tpl');
            }
        }
    }
}
