<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class FrontendAccount
 * @package ArboroGoogleTracking\Subscriber
 */
class FrontendAccount extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Frontend_Account';

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

        if($this->shouldTrack('enhancedEcommerce')) {
            if($request->getActionName() === 'ajax_login') {
                $this->addTemplateToView('account/ajax_login_emotion.tpl');
                $this->assign('trackingType', $this->getTrackingType());

                if($this->isTagManager()) {
                    $this->assign('dataLayerName');
                }

                $this->assign('checkoutStep', 2);
            }
        }
    }
}
