<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\Decorations;

use Shopware\Models\Plugin\Plugin;
use Symfony\Component\DependencyInjection\Container;
use \Shopware\Bundle\PluginInstallerBundle\Service\LegacyPluginInstaller as SwLegacyPluginInstaller;

/**
 * Class LegacyPluginInstaller
 *
 * @package NetiFoundation\Service
 */
class LegacyPluginInstaller extends SwLegacyPluginInstaller
{
    /**
     * @var Container
     */
    protected $container; // TODO: try to avoid injecting the container itself, inject the required services instead

    /**
     * @var SwLegacyPluginInstaller
     */
    protected $coreService;

    /**
     * @var PluginManager
     */
    private   $pluginManager;

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * @param SwLegacyPluginInstaller $coreService
     * @param PluginManager           $pluginManager
     */
    public function __construct(
        SwLegacyPluginInstaller $coreService,
        PluginManager $pluginManager
    )
    {
        $this->coreService = $coreService;
        $this->pluginManager = $pluginManager;
    }

    /**
     * @param Plugin $plugin
     *
     * @return null|\Shopware_Components_Plugin_Bootstrap
     */
    public function getPluginBootstrap(Plugin $plugin)
    {
        return $this->coreService->getPluginBootstrap($plugin);
    }

    /**
     * @param Plugin $plugin
     *
     * @return array
     * @throws \Exception
     */
    public function installPlugin(Plugin $plugin)
    {
        return $this->coreService->installPlugin($plugin);
    }

    /**
     * @param Plugin    $plugin
     * @param bool|true $removeData
     *
     * @return array
     * @throws \Exception
     */
    public function uninstallPlugin(Plugin $plugin, $removeData = true)
    {
        if ($plugin->getName() === $this->getFoundationPluginName()) {
            $this->itIsRequired($plugin);
        }

        return $this->coreService->uninstallPlugin($plugin, $removeData);
    }

    /**
     * @param Plugin $plugin
     *
     * @return array
     * @throws \Exception
     */
    public function updatePlugin(Plugin $plugin)
    {
        return $this->coreService->updatePlugin($plugin);
    }

    /**
     * @param Plugin $plugin
     *
     * @return array
     * @throws \Exception
     */
    public function activatePlugin(Plugin $plugin)
    {
        return $this->coreService->activatePlugin($plugin);
    }

    /**
     * @param Plugin $plugin
     *
     * @return array|bool
     * @throws \Exception
     */
    public function deactivatePlugin(Plugin $plugin)
    {
        if ($plugin->getName() === $this->getFoundationPluginName()) {
            $this->itIsRequired($plugin);
        }

        return $this->coreService->deactivatePlugin($plugin);
    }

    /**
     * @param \DateTimeInterface $refreshDate
     *
     * @throws \Exception
     */
    public function refreshPluginList(\DateTimeInterface $refreshDate)
    {
        $this->coreService->refreshPluginList($refreshDate);
    }

    /**
     * @param Plugin $plugin
     *
     * @return string
     * @throws \Exception
     */
    public function getPluginPath(Plugin $plugin)
    {
        return $this->coreService->getPluginPath($plugin);
    }

    /**
     * @param Plugin      $plugin
     * @param string|null $pluginName
     *
     * @throws \Enlight_Exception
     */
    protected function itIsRequired(Plugin $plugin, $pluginName = null)
    {
        $this->pluginManager->itIsRequired($plugin, $pluginName);
    }

    /**
     * Gets the value of foundationPluginName from the record
     *
     * @return string
     */
    public function getFoundationPluginName()
    {
        return $this->pluginManager->getFoundationPluginName();
    }

    /**
     * Gets the value of foundationPlugin from the record
     *
     * @return Plugin
     */
    public function getFoundationPlugin()
    {
        return $this->pluginManager->getFoundationPlugin();
    }

}
