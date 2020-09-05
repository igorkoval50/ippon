<?php

namespace ArboroGoogleTracking\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CompilerCollectJavaScript
 * @package ArboroGoogleTracking\Subscriber
 */
class CompilerCollectJavaScript extends AbstractSubscriber
{
    const EVENT = 'Theme_Compiler_Collect_Plugin_Javascript';

    /**
     * @param \Enlight_Event_EventArgs $args
     *
     * @return string
     */
    public function onDispatch($args)
    {
        $pluginDir = $this->container->getParameter('arboro_google_tracking.plugin_dir');
        $jsFiles = [
            $pluginDir . '/Resources/views/frontend/_resources/javascript/ArboroGoogleTracking.min.js',
            $pluginDir . '/Resources/views/frontend/_resources/javascript/autotrack.js',
        ];

        return new ArrayCollection($jsFiles);
    }
}