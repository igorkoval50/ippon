<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class WidgetsRecommendation
 * @package ArboroGoogleTracking\Subscriber
 */
class WidgetsRecommendation extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatch_Widgets_Recommendation';

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

        if(($actionName === 'viewed' || $actionName === 'bought') && $this->shouldTrack('enhancedEcommerce')) {
            $this->assign('trackingType', $this->getTrackingType());

            if($this->isTagManager()) {
                $this->assign('dataLayerName');
            }

            if($actionName === 'viewed' && count($this->view->viewedArticles)) {
                $this->assign('template', 'viewed');
                $this->assign('sArticles', $this->view->viewedArticles);
            }

            if($actionName === 'bought' && count($this->view->boughtArticles)) {
                $this->assign('template', 'bought');
                $this->assign('sArticles', $this->view->boughtArticles);
            }

            $this->addTemplateToView('widgets/recommendation_extended.tpl');
        }
    }
}
