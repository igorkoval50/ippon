<?php

declare(strict_types=1);

namespace NetiFoundation\Subscriber;

use Enlight\Event\SubscriberInterface;
use NetiFoundation\Service\{PluginManager as PluginManagerService, PluginManager\Base};
use Shopware\Bundle\PluginInstallerBundle\Events\{PluginEvent,
    PrePluginActivateEvent,
    PrePluginDeactivateEvent,
    PrePluginInstallEvent,
    PrePluginUninstallEvent,
    PrePluginUpdateEvent};
use Shopware\Components\Plugin;

class PluginInstaller implements SubscriberInterface
{
    /**
     * @var PluginManagerService
     */
    private $pluginManager;

    /**
     * @var Base
     */
    private $pluginManagerBase;

    /**
     * PluginInstaller constructor.
     *
     * @param PluginManagerService $pluginManager
     * @param Base                 $pluginManagerBase
     */
    public function __construct(PluginManagerService $pluginManager, Base $pluginManagerBase)
    {
        $this->pluginManager     = $pluginManager;
        $this->pluginManagerBase = $pluginManagerBase;
    }

    /**
     * @noinspection ReturnTypeCanBeDeclaredInspection
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            PluginEvent::PRE_INSTALL    => 'preInstall',
            PluginEvent::PRE_UNINSTALL  => 'preUninstall',
            PluginEvent::PRE_UPDATE     => 'preUpdate',
            PluginEvent::PRE_ACTIVATE   => 'preActivate',
            PluginEvent::PRE_DEACTIVATE => 'preDeactivate',
        ];
    }

    public function preInstall(PrePluginInstallEvent $event): void
    {
        $plugin = $event->getPlugin();

        if ($this->pluginManager->checkRequiredPlugin($plugin)) {
            $this->pluginManager->installEvent($plugin, $this->pluginManagerBase->getConfigFile($plugin));
        }
    }

    public function preActivate(PrePluginActivateEvent $event): void
    {
        $plugin = $event->getPlugin();

        if ($this->pluginManager->checkRequiredPlugin($plugin)) {
            $this->pluginManager->activateEvent($plugin, $this->pluginManagerBase->getConfigFile($plugin));
        }
    }

    public function preDeactivate(PrePluginDeactivateEvent $event): void
    {
        $plugin = $event->getPlugin();

        if ($this->pluginManager->checkRequiredPlugin($plugin)) {
            $this->pluginManager->deactivateEvent($plugin, $this->pluginManagerBase->getConfigFile($plugin));
        }
    }

    public function preUpdate(PrePluginUpdateEvent $event): void
    {
        $plugin = $event->getPlugin();
        /** @var Plugin\Context\UpdateContext $context */
        $context = $event->getContext();

        if ($this->pluginManager->checkRequiredPlugin($plugin)) {
            $this->pluginManager->updateEvent($plugin, $this->pluginManagerBase->getConfigFile($plugin), $context);
        }
    }

    /**
     * @param PrePluginUninstallEvent $event
     *
     * @throws \Enlight_Exception
     */
    public function preUninstall(PrePluginUninstallEvent $event): void
    {
        /** @var Plugin\Context\UninstallContext $context */
        $context = $event->getContext();
        $plugin  = $event->getPlugin();

        $this->pluginManager->itIsRequired($plugin->getName());

        if (!$context->keepUserData()) {
            $this->pluginManager->uninstallEvent(
                $plugin,
                $this->pluginManagerBase->getConfigFile($plugin)
            );
        }
    }
}