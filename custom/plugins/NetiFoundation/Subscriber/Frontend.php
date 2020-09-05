<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Subscriber;


use Enlight\Event\SubscriberInterface;
use NetiFoundation\Service\PluginManager\Config;
use NetiFoundation\Struct\PluginConfig;

class Frontend implements SubscriberInterface
{
    /** @var Config */
    private $configService;

    /**
     * Frontend constructor.
     * @param Config $configService
     * @param \Enlight_Controller_Front $frontController
     */
    public function __construct(Config $configService, \Enlight_Controller_Front $frontController)
    {
        $this->configService = $configService;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch',
            'Enlight_Controller_Action_PostDispatchSecure' => 'onPostDispatch'
        );
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        $subject  = $args->getSubject();
        $request  = $subject->Request();
        $module   = $request->getModuleName();

        if (
            'frontend' !== $module
            && 'widgets' !== $module
        ) {
            return;
        }

        $subject->View()->assign('netiRequestData', [
            'module'     => $request->getModuleName(),
            'controller' => $request->getControllerName(),
            'action'     => $request->getActionName()
        ]);
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onPreDispatch(\Enlight_Event_EventArgs $args)
    {
        $context  = $args->get('subject');
        /** @var \Enlight_Controller_Request_RequestHttp $request */
        $request  = $context->Request();
        $module = $request->getModuleName();

        if ('backend' === $module || 'api' === $module) {
            return;
        }

        // Enable debug output
        /** @var PluginConfig $config */
        $config = $this->configService->getPluginConfig('NetiFoundation');
        if (true === $config->isShowDebug()) {
            $front = Shopware()->Container()->get('front');
            if (null !== $front) {
                $front->setParam('showException', true);
            }
        }
    }
}
