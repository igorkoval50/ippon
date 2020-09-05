<?php

declare(strict_types=1);

namespace NetiLanguageDetector\Subscriber;

use Enlight\Event\SubscriberInterface;
use NetiFoundation\Service\PluginManager\Config;
use NetiLanguageDetector\Struct\PluginConfig;
use Symfony\Component\HttpKernel\KernelEvents;

class Request implements SubscriberInterface
{
    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @var PluginConfig
     */
    private $pluginConfig;

    /**
     * @throws \Exception
     */
    public function __construct(
        \Enlight_Components_Session_Namespace $session,
        Config $configService
    ) {
        $this->session      = $session;
        $this->pluginConfig = $configService->getPluginConfig($this);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::TERMINATE => 'onKernelTerminate'];
    }

    public function onKernelTerminate(): void
    {
        if (!$this->pluginConfig instanceof PluginConfig) {
            return;
        }

        if ($this->pluginConfig->isDebugMode()) {
            $this->session->offsetSet('netiLanguageDetector', []);
        }
    }
}