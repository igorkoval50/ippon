<?php

namespace ArboroGoogleTracking\Subscriber;

/**
 * Class ArboroGoogleTrackingController
 * @package ArboroGoogleTracking\Subscriber
 */
class ArboroGoogleTrackingController extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Dispatcher_ControllerPath_Frontend_ArboroGoogleTracking';

    /**
     * @param \Enlight_Event_EventArgs $args
     *
     * @return string
     */
    public function onDispatch($args)
    {
        return $this->container->getParameter(
                'arboro_google_tracking.plugin_dir'
            ) . '/Controllers/Frontend/ArboroGoogleTracking.php';
    }
}