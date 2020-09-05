<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\Decorations;

use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Service\PluginManager\BaseInterface;
use NetiFoundation\Service\PluginManagerInterface;
use NetiFoundation\Struct\PluginConfigFile;
use NetiFoundation\Struct\PluginConfigFileInterface;
use Shopware\Bundle\PluginInstallerBundle\Service\PluginInstaller as SwPluginInstaller;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin as PluginBootstrap;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\MenuSynchronizer;
use Shopware\Components\Plugin\RequirementValidator;
use Shopware\Components\Plugin\XmlMenuReader;
use Shopware\Models\Plugin\Plugin;

/**
 * Class PluginInstaller
 *
 * @package NetiFoundation\Service
 */
class PluginInstaller extends SwPluginInstaller
{
    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var SwPluginInstaller
     */
    protected $coreService;

    /**
     * @var RequirementValidator
     */
    protected $requirementValidator;

    /**
     * @var LoggingServiceInterface
     */
    protected $loggingService;

    /**
     * @var PluginManagerInterface
     */
    private $pluginManager;

    /**
     * @var \Shopware_Components_Config
     */
    private $swConfig;

    /**
     * @var BaseInterface
     */
    protected $pluginManagerBase;

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * @param ModelManager                $em
     * @param RequirementValidator        $requirementValidator
     * @param SwPluginInstaller           $coreService
     * @param LoggingServiceInterface     $loggingService
     * @param PluginManagerInterface      $pluginManager
     * @param \Shopware_Components_Config $swConfig
     * @param BaseInterface               $pluginManagerBase
     */
    public function __construct(
        ModelManager $em,
        RequirementValidator $requirementValidator,
        SwPluginInstaller $coreService,
        LoggingServiceInterface $loggingService,
        PluginManagerInterface $pluginManager,
        \Shopware_Components_Config $swConfig,
        BaseInterface $pluginManagerBase
    ) {
        $this->em                   = $em;
        $this->coreService          = $coreService;
        $this->loggingService       = $loggingService;
        $this->requirementValidator = $requirementValidator;
        $this->pluginManager        = $pluginManager;
        $this->swConfig             = $swConfig;
        $this->pluginManagerBase    = $pluginManagerBase;
    }

    /**
     * @param Plugin $plugin
     *
     * @return InstallContext
     * @throws \Exception
     */
    public function installPlugin(Plugin $plugin)
    {
        if ($this->checkRequiredPlugin($plugin)) {
            $requiredFoundationVersion = $this->getRequiredFoundationVersion($plugin);

            if (! $this->compareFoundationVersion($requiredFoundationVersion)) {
                $exception = 'Bitte installieren und aktivieren Sie zunächst unser Basis-Plugin '
                    . '<a href="http://store.shopware.com/detail/index/sArticle/162025" target="_blank">'
                    . 'NetiFoundation</a> in der Version ' . $requiredFoundationVersion . ' oder höher.';
                throw new \Enlight_Exception($exception);
            }

            $this->loggingService->write(
                $plugin->getName(),
                __FUNCTION__,
                'Plugin version ' . $plugin->getVersion()
            );

            $this->installEvent($plugin);
        }

        return $this->coreService->installPlugin($plugin);
    }

    /**
     * @param Plugin      $plugin
     * @param string|null $pluginName
     *
     * @return bool
     */
    public function checkRequiredPlugin(Plugin $plugin, $pluginName = null)
    {
        return $this->pluginManager->checkRequiredPlugin($plugin, $pluginName);
    }

    /**
     * @param Plugin $plugin
     *
     * @return mixed
     */
    protected function getRequiredFoundationVersion(Plugin $plugin)
    {
        if ($this->checkRequiredPlugin($plugin, $this->getFoundationPluginName())) {
            $info = $this->pluginManager->getRequiredFromPluginXml($plugin, $this->getFoundationPluginName());

            return $info['minVersion'];
        }

        return null;
    }

    /**
     * Gets the value of foundationPluginName from the record
     *
     * @return string
     */
    protected function getFoundationPluginName()
    {
        return $this->pluginManager->getFoundationPluginName();
    }

    /**
     * @param $requiredFoundationVersion
     *
     * @return boolean|null
     */
    protected function compareFoundationVersion($requiredFoundationVersion)
    {
        $foundationPlugin = $this->getFoundationPlugin();

        if ($foundationPlugin instanceof Plugin) {
            return version_compare($foundationPlugin->getVersion(), $requiredFoundationVersion, '>=');
        }

        return null;
    }

    /**
     * Gets the value of foundationPlugin from the record
     *
     * @return Plugin
     */
    protected function getFoundationPlugin()
    {
        return $this->pluginManager->getFoundationPlugin();
    }

    /**
     * @param Plugin $plugin
     *
     * @throws \Exception
     */
    public function installEvent(Plugin $plugin)
    {
        $configFile = $this->getConfigFile($plugin);

        $this->pluginManager->installEvent($plugin, $configFile);
    }

    /**
     * @param Plugin $plugin
     *
     * @return PluginConfigFileInterface
     */
    public function getConfigFile(Plugin $plugin)
    {
        return $this->pluginManagerBase->getConfigFile($plugin);
    }

    /**
     * @param Plugin $plugin
     *
     * @return string
     */
    public function getPluginPath(Plugin $plugin)
    {
        return $this->coreService->getPluginPath($plugin);
    }

    /**
     * @param Plugin $plugin
     * @param bool   $removeData
     *
     * @return bool
     * @throws \Exception
     */
    public function uninstallPlugin(Plugin $plugin, $removeData = true)
    {
        if ($plugin->getName() === $this->getFoundationPluginName()) {
            $this->itIsRequired($plugin);
        }

        if ($this->checkRequiredPlugin($plugin)) {
            $this->loggingService->write(
                $plugin->getName(),
                __FUNCTION__
            );

            $this->itIsRequired($plugin, $plugin->getName());

            // this function removes almost all parts of the plugin (tables, attributes, mail templates, etc.)
            // If someone accidentially uninstalls the plugin, he will lose all of the data
            // In later 5.2 versions, there isn explicit question in the uninstall process, whether to remove or keep the plugins data
            // We should only remove all of the data, if the user confirms the removal

            // $this->uninstallEvent($plugin, $removeData);
        }

        return $this->coreService->uninstallPlugin($plugin, $removeData);
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
     * @param Plugin $plugin
     *
     * @return bool
     * @throws \Exception
     */
    public function updatePlugin(Plugin $plugin)
    {
        if ($this->checkRequiredPlugin($plugin)) {
            $this->loggingService->write(
                $plugin->getName(),
                __FUNCTION__,
                'Plugin version ' . $plugin->getVersion() . ', Update version ' . $plugin->getUpdateVersion()
            );

            $this->updateEvent($plugin);
        }

        return $this->coreService->updatePlugin($plugin);
    }

    /**
     * @param Plugin $plugin
     *
     * @throws \Exception
     */
    public function updateEvent(Plugin $plugin)
    {
        $configFile = $this->getConfigFile($plugin);

        $this->pluginManager->updateEvent($plugin, $configFile);
    }

    /**
     * @param Plugin $plugin
     *
     * @return array|bool|void
     * @throws \Exception
     */
    public function activatePlugin(Plugin $plugin)
    {
        if ($this->checkRequiredPlugin($plugin)) {
            $this->loggingService->write(
                $plugin->getName(),
                __FUNCTION__
            );

            $this->checkPluginRequirements($plugin);

            $this->activateEvent($plugin);
        }

        return $this->coreService->activatePlugin($plugin);
    }

    /**
     * @param Plugin $plugin
     */
    public function checkPluginRequirements(Plugin $plugin)
    {
        $pluginBootstrap = $this->getPluginByName($plugin->getName());
        $this->requirementValidator->validate(
            $pluginBootstrap->getPath() . '/plugin.xml',
            $this->swConfig->get('Version')
        );
    }

    /**
     * @param string $pluginName
     *
     * @return PluginBootstrap
     */
    public function getPluginByName($pluginName)
    {
        return $this->pluginManager->getPluginByName($pluginName);
    }

    /**
     * @param Plugin $plugin
     */
    public function activateEvent(Plugin $plugin)
    {
        $configFile = $this->getConfigFile($plugin);

        $this->checkPluginRequirements($plugin);

        $this->pluginManager->activateEvent($plugin, $configFile);
    }

    /**
     * @param Plugin $plugin
     *
     * @return array|bool|void
     * @throws \Exception
     */
    public function deactivatePlugin(Plugin $plugin)
    {
        if ($plugin->getName() === $this->getFoundationPluginName()) {
            $this->itIsRequired($plugin);
        }

        if ($this->checkRequiredPlugin($plugin)) {
            $this->loggingService->write(
                $plugin->getName(),
                __FUNCTION__
            );

            $this->itIsRequired($plugin, $plugin->getName());

            $this->deactivateEvent($plugin);
        }

        return $this->coreService->deactivatePlugin($plugin);
    }

    /**
     * @param Plugin $plugin
     */
    public function deactivateEvent(Plugin $plugin)
    {
        $configFile = $this->getConfigFile($plugin);

        $this->pluginManager->deactivateEvent($plugin, $configFile);
    }

    /**
     * @param \DateTimeInterface $refreshDate
     */
    public function refreshPluginList(\DateTimeInterface $refreshDate)
    {
        $this->coreService->refreshPluginList($refreshDate);
    }

    /**
     * @param Plugin $plugin
     * @param bool   $removeData
     */
    public function uninstallEvent(Plugin $plugin, $removeData = true)
    {
        $configFile = $this->getConfigFile($plugin);

        $this->pluginManager->uninstallEvent($plugin, $removeData, $configFile);
    }

    /**
     * @param Plugin $plugin
     * @param string $file
     */
    public function installMenu(Plugin $plugin, $file)
    {
        $menuReader = new XmlMenuReader();
        $menu       = $menuReader->read($file);

        $menuSynchronizer = new MenuSynchronizer($this->em);
        $menuSynchronizer->synchronize($plugin, $menu);
    }
}
