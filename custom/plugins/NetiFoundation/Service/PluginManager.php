<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile;
use NetiFoundation\Updates\AbstractUpdate;
use Shopware\Bundle\AttributeBundle\Service\CrudService as AttributeService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping as AttributeTypeMapping;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\XmlReader\XmlPluginReader;
use Shopware\Kernel;
use Shopware\Models\Plugin\Plugin as PluginModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginManager
 *
 * @package NetiFoundation\Service
 */
class PluginManager implements PluginManagerInterface
{
    /**
     * @var array[Plugin]
     */
    public static $pluginModelCache = array();

    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var AttributeService
     */
    protected $attributeService;

    /**
     * @var AttributeTypeMapping
     */
    protected $attributeTypeMapping;

    /**
     * @var \Shopware_Components_Translation
     */
    protected $translation;

    /**
     * @var \Shopware_Components_Acl
     */
    protected $acl;

    /**
     * @var PluginManager\Schema
     */
    protected $pluginManagerSchema;

    /**
     * @var PluginManager\Attributes
     */
    protected $pluginManagerAttributes;

    /**
     * @var PluginManager\Indexes
     */
    protected $pluginManagerIndexes;

    /**
     * @var PluginManager\MailTemplates
     */
    protected $pluginManagerMailTemplates;

    /**
     * @var PluginManager\Acl
     */
    protected $pluginManagerAcl;

    /**
     * @var PluginManager\Form
     */
    protected $pluginManagerForm;

    /**
     * @var PluginManager\Menu
     */
    protected $pluginManagerMenu;

    /**
     * @var PluginManager\Media
     */
    protected $pluginManagerMedia;

    /**
     * @var LoggingServiceInterface
     */
    protected $loggingService;

    /**
     * @var PluginManager\Cron
     */
    protected $pluginManagerCron;

    /**
     * @var Container
     */
    protected $container; // TODO: try to avoid injecting the container itself, inject the required services instead

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var string
     */
    protected $foundationPluginName = 'NetiFoundation';

    /**
     * @var Plugin
     */
    protected $foundationPlugin;

    /**
     * @var XmlPluginReader
     */
    protected $infoReader;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    protected $db;

    /**
     * @var PluginManager\Config
     */
    protected $pluginManagerConfig;

    /**
     * @var array
     */
    protected $pluginVersions = [];

    /**
     * @param ModelManager                             $em
     * @param ContainerInterface                       $container
     * @param Connection                               $connection
     * @param PluginManager\Schema                     $pluginManagerSchema
     * @param PluginManager\Attributes                 $pluginManagerAttributes
     * @param PluginManager\Indexes                    $pluginManagerIndexes
     * @param PluginManager\Acl                        $pluginManagerAcl
     * @param PluginManager\MailTemplates              $pluginManagerMailTemplates
     * @param PluginManager\Form                       $pluginManagerForm
     * @param PluginManager\Menu                       $pluginManagerMenu
     * @param PluginManager\Media                      $pluginManagerMedia
     * @param LoggingServiceInterface                  $loggingService
     * @param PluginManager\Cron                       $pluginManagerCron
     * @param PluginManager\Config                     $pluginManagerConfig
     * @param XmlPluginReader                          $infoReader
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     */
    public function __construct(
        ModelManager $em,
        ContainerInterface $container,
        Connection $connection,
        PluginManager\Schema $pluginManagerSchema,
        PluginManager\Attributes $pluginManagerAttributes,
        PluginManager\Indexes $pluginManagerIndexes,
        PluginManager\Acl $pluginManagerAcl,
        PluginManager\MailTemplates $pluginManagerMailTemplates,
        PluginManager\Form $pluginManagerForm,
        PluginManager\Menu $pluginManagerMenu,
        PluginManager\Media $pluginManagerMedia,
        LoggingServiceInterface $loggingService,
        PluginManager\Cron $pluginManagerCron,
        PluginManager\Config $pluginManagerConfig,
        XmlPluginReader $infoReader,
        \Enlight_Components_Db_Adapter_Pdo_Mysql $db
    ) {
        $this->em                         = $em;
        $this->container                  = $container;
        $this->connection                 = $connection;
        $this->pluginManagerSchema        = $pluginManagerSchema;
        $this->pluginManagerAttributes    = $pluginManagerAttributes;
        $this->pluginManagerIndexes       = $pluginManagerIndexes;
        $this->pluginManagerAcl           = $pluginManagerAcl;
        $this->pluginManagerMailTemplates = $pluginManagerMailTemplates;
        $this->pluginManagerForm          = $pluginManagerForm;
        $this->pluginManagerMenu          = $pluginManagerMenu;
        $this->pluginManagerMedia         = $pluginManagerMedia;
        $this->loggingService             = $loggingService;
        $this->pluginManagerCron          = $pluginManagerCron;
        $this->pluginManagerConfig        = $pluginManagerConfig;
        $this->infoReader                 = $infoReader;
        $this->db                         = $db;
    }

    /**
     * @param Plugin           $plugin
     * @param PluginConfigFile $configFile
     *
     * @throws DBALException
     */
    public function installEvent(Plugin $plugin, PluginConfigFile $configFile): void
    {
        if (method_exists($plugin, 'preFoundationEventCall')) {
            $plugin->preFoundationEventCall(self::EVENT_INSTALL, $plugin);
        }

        $models     = $configFile->getModels();
        $attributes = $configFile->getAttributes();
        $indexes    = $configFile->getIndexes();
        $form       = $configFile->getForm();
        $acl        = $configFile->getAcl();
        $media      = $configFile->getMedia();
        $cronJobs   = $configFile->getCronJobs();

        if (!empty($form)) {
            $this->pluginManagerForm->installForm($plugin, $form);
        }

        if (!empty($models)) {
            $this->pluginManagerSchema->createSchema($plugin, $models);
        }

        if (!empty($attributes)) {
            $this->pluginManagerAttributes->createAttributes($plugin, $attributes);
        }

        if (!empty($indexes)) {
            $this->pluginManagerIndexes->createIndexes($plugin, $indexes);
        }

        if (!empty($acl)) {
            $this->pluginManagerAcl->installAcl($plugin, $acl);
        }

        if (null !== $media) {
            $albums = $media->getAlbums();
            if (!empty($albums)) {
                $this->pluginManagerMedia->addAlbums($plugin, $albums);
            }
        }

        if (!empty($cronJobs)) {
            $this->pluginManagerCron->addCronJobs($plugin, $cronJobs);
        }

        if ($this->hasPluginResource($plugin, '/Views/mails')) {
            $this->pluginManagerMailTemplates->installMailTemplates(
                $plugin,
                $this->getPluginResource($plugin, '/Views/mails')
            );
        }

        if ($this->hasPluginResource($plugin, '/Resources/mails')) {
            $this->pluginManagerMailTemplates->installMailTemplates(
                $plugin,
                $this->getPluginResource($plugin, '/Resources/mails')
            );
        }
    }

    /**
     * @param Plugin           $plugin
     * @param PluginConfigFile $configFile
     * @param UpdateContext    $context
     *
     * @throws DBALException
     * @throws \Enlight_Exception
     * @throws \ReflectionException
     */
    public function updateEvent(Plugin $plugin, PluginConfigFile $configFile, UpdateContext $context): void
    {
        if (method_exists($plugin, 'preFoundationEventCall')) {
            $plugin->preFoundationEventCall(self::EVENT_UPDATE, $plugin);
        }

        $models     = $configFile->getModels();
        $attributes = $configFile->getAttributes();
        $indexes    = $configFile->getIndexes();
        $form       = $configFile->getForm();
        $acl        = $configFile->getAcl();
        $media      = $configFile->getMedia();
        $cronJobs   = $configFile->getCronJobs();

        if ($this->hasPluginResource($plugin, '/Updates')) {
            $this->executeUpdateFiles(
                $plugin,
                AbstractUpdate::RUN_BEFORE_GENERAL_UPDATE,
                $context
            );
        }

        $this->pluginManagerForm->updateForm($plugin, $form);

        if (! empty($models)) {
            $this->pluginManagerSchema->updateSchema($plugin, $models);
        }

        if (! empty($attributes)) {
            $this->pluginManagerAttributes->updateAttributes($plugin, $attributes);
        }

        if (! empty($indexes)) {
            $this->pluginManagerIndexes->updateIndexes($plugin, $indexes);
        }

        if (! empty($acl)) {
            $this->pluginManagerAcl->updateAcl($plugin, $acl);
        }

        if (null !== $media) {
            $albums = $media->getAlbums();
            if (! empty($albums)) {
                $this->pluginManagerMedia->updateAlbums($plugin, $albums);
            }
        }

        if (! empty($cronJobs)) {
            $this->pluginManagerCron->updateCronJobs($plugin, $cronJobs);
        }

        if ($this->hasPluginResource($plugin, '/Views/mails')) {
            $this->pluginManagerMailTemplates->updateMailTemplates(
                $plugin,
                $this->getPluginResource($plugin, '/Views/mails')
            );
        }

        if ($this->hasPluginResource($plugin, '/Resources/mails')) {
            $this->pluginManagerMailTemplates->updateMailTemplates(
                $plugin,
                $this->getPluginResource($plugin, '/Resources/mails')
            );
        }

        if ($this->hasPluginResource($plugin, '/Updates')) {
            $this->executeUpdateFiles(
                $plugin,
                AbstractUpdate::RUN_AFTER_GENERAL_UPDATE,
                $context
            );
        }
    }

    /**
     * @param Plugin $plugin
     * @param string $resource
     *
     * @return bool
     *
     * @deprecated will be private in 5.0.0
     */
    public function hasPluginResource(Plugin $plugin, string $resource): bool
    {
        return file_exists($plugin->getPath() . $resource);
    }

    /**
     * @param Plugin        $plugin
     * @param string        $runningOrder (see RUN_-constants in \NetiFoundation\Updates\AbstractUpdate)
     * @param UpdateContext $context
     *
     * @throws \Enlight_Exception
     * @throws \ReflectionException
     */
    protected function executeUpdateFiles(Plugin $plugin, $runningOrder, UpdateContext $context): void
    {
        /** @var array $versions */
        $versions  = $this->getVersions($plugin, $context);
        $namespace = sprintf('%s\Updates\\', $plugin->getName());

        foreach ($versions as $version => $file) {
            /**
             * @var \SplFileInfo $file
             */

            include_once $file->getPathname();

            $className = sprintf('%sUpdate_%s', $namespace, $file->getBasename('.php'));

            if (! class_exists($className)) {
                $this->loggingService->write(
                    $plugin,
                    __FUNCTION__,
                    sprintf('Update class %s does not exist.', $className)
                );

                throw new \Enlight_Exception(sprintf('Update class %s does not exist.', $className));
            }

            $reflectionClass = new \ReflectionClass($className);
            $reflectionClass->newInstanceArgs(array(
                $plugin,
                $context->getPlugin(),
                $this->em,
                $this->db,
                $version,
                $runningOrder,
            ));
        }
    }

    /**
     * @param Plugin $plugin
     * @param string $resource
     *
     * @return string
     *
     * @deprecated will be private in 5.0.0
     */
    public function getPluginResource(Plugin $plugin, string $resource): string
    {
        return $plugin->getPath() . $resource;
    }

    /**
     * @param Plugin           $plugin
     * @param PluginConfigFile $configFile
     */
    public function activateEvent(Plugin $plugin, PluginConfigFile $configFile): void
    {
        $menus = $configFile->getMenu();
        if (! empty($menus)) {
            $this->pluginManagerMenu->installMenu($plugin, $menus);
        }
    }

    /**
     * @param Plugin           $plugin
     * @param bool             $removeData
     * @param PluginConfigFile $configFile
     */
    public function uninstallEvent(Plugin $plugin, PluginConfigFile $configFile, $removeData = true): void
    {
        $acl        = $configFile->getAcl();
        $models     = $configFile->getModels();
        $attributes = $configFile->getAttributes();
        $indexes    = $configFile->getIndexes();
        $media      = $configFile->getMedia();

        if (true === $removeData) {
            if (! empty($acl)) {
                $this->pluginManagerAcl->removeAcl($plugin);
            }

            if (! empty($models)) {
                $this->pluginManagerSchema->removeSchema($plugin, $models);
            }

            if (! empty($attributes)) {
                $this->pluginManagerAttributes->removeAttributes($plugin, $attributes);
            }

            if (! empty($indexes)) {
                $this->pluginManagerIndexes->removeIndexes($plugin, $indexes);
            }

            if (null !== $media) {
                $albums = $media->getAlbums();
                if (! empty($albums)) {
                    $this->pluginManagerMedia->removeAlbums($plugin, $albums);
                }
            }

            if ($this->hasPluginResource($plugin, '/Views/mails')) {
                $this->pluginManagerMailTemplates->removeMailTemplates(
                    $plugin,
                    $this->getPluginResource($plugin, '/Views/mails')
                );
            }
            // TODO: Are cronjobs and menu items removed automatically?
        }
    }

    /**
     * @param Plugin           $plugin
     * @param PluginConfigFile $configFile
     *
     * @throws DBALException
     */
    public function deactivateEvent(Plugin $plugin, PluginConfigFile $configFile): void
    {
        $menu = $configFile->getMenu();
        if (! empty($menu)) {
            $this->pluginManagerMenu->removeMenu($plugin);
        }
    }

    /**
     * @param string|null $pluginName
     *
     * @throws \Enlight_Exception
     */
    public function itIsRequired(?string $pluginName = null): void
    {
        if (!$pluginName) {
            $pluginName = $this->getFoundationPluginName();
        }

        $plugins       = [];
        $activePlugins = $this->getKernel()->getPlugins();
        foreach ($activePlugins as $activePlugin) {
            if (
                $activePlugin->isActive()
                && $this->checkRequiredPlugin($activePlugin, $pluginName)
            ) {
                $plugins[] = $activePlugin->getName();
            }
        }

        if (!empty($plugins)) {
            $exception = sprintf(
                'The plugin "%s" is required for the following plugins:<br />' .
                '<ul><li>' .
                implode('</li><li>', $plugins) .
                '</li></ul>',
                $pluginName
            );
            throw new \Enlight_Exception($exception);
        }
    }

    /**
     * Gets the value of foundationPluginName from the record
     *
     * @return string
     */
    public function getFoundationPluginName()
    {
        return $this->foundationPluginName;
    }

    /**
     * @return \Shopware\Kernel
     */
    protected function getKernel()
    {
        return $this->container->get('kernel');
    }

    /**
     * @param string $pluginName
     *
     * @return null|PluginModel
     */
    public function getPluginModelByName(string $pluginName): ?PluginModel
    {
        $pluginName = strtolower($pluginName);

        if (\array_key_exists($pluginName, self::$pluginModelCache)) {
            return self::$pluginModelCache[$pluginName];
        }

        $repository = $this->em->getRepository(PluginModel::class);
        $plugin     = $repository->findOneBy([
            'name'      => $pluginName,
            'namespace' => 'ShopwarePlugins',
        ]);

        return self::$pluginModelCache[$pluginName] = $plugin;
    }

    /**
     * @param Plugin      $plugin
     * @param string|null $pluginName
     *
     * @return bool
     */
    public function checkRequiredPlugin(Plugin $plugin, ?string $pluginName = null): bool
    {
        if (!$pluginName) {
            $pluginName = $this->getFoundationPluginName();
        }

        return is_array($this->getRequiredFromPluginXml($plugin, $pluginName));
    }

    /**
     * @param Plugin $plugin
     * @param string $pluginName
     *
     * @return array|null
     */
    public function getRequiredFromPluginXml(Plugin $plugin, string $pluginName): ?array
    {
        if (!is_readable($plugin->getPath() . '/plugin.xml')) {
            return null;
        }

        $info = $this->infoReader->read($plugin->getPath() . '/plugin.xml');

        if (isset($info['requiredPlugins'])) {
            $requiredPlugins = $info['requiredPlugins'];
            foreach ($requiredPlugins as $requiredPlugin) {
                if ($requiredPlugin['pluginName'] === $pluginName) {
                    return $requiredPlugin;
                }
            }
        }

        return null;
    }

    /**
     * @param string $pluginName
     *
     * @return bool
     */
    public function isAvailable($pluginName)
    {
        $plugin = $this->getPluginModelByName($pluginName);

        return ($plugin instanceof PluginModel) && $plugin->getActive();
    }

    /**
     * @param string $pluginName
     *
     * @return Plugin
     */
    public function getPluginByName($pluginName)
    {
        $plugins = $this->getKernel()->getPlugins();

        if (! isset($plugins[$pluginName])) {
            throw new \InvalidArgumentException(sprintf('Plugin by name "%s" not found.', $pluginName));
        }

        return $plugins[$pluginName];
    }

    /**
     * @param Plugin|object|string $plugin
     *
     * @return \NetiFoundation\Struct\PluginConfig|object
     * @throws \Exception
     *
     * @deprecated 4.0.0 - just inject the config service maybe?
     */
    public function getPluginConfig($plugin)
    {
        return $this->pluginManagerConfig->getPluginConfig($plugin);
    }

    /**
     * @param PluginModel|string $plugin
     *
     * @return Plugin
     *
     * @deprecated since 4.0.0 - will be removed in 5.0.0
     * @see        getPluginByName
     */
    public function getPluginBootstrap($plugin): Plugin
    {
        \trigger_error(\sprintf(
            '%s is deprecated since 4.0.0, will be removed in 5.0.0. Use %s::getPluginByName instead',
            __METHOD__,
            __CLASS__
        ), \E_USER_DEPRECATED);

        //added because I'm not entirely sure there aren't any calls like this.
        // this is why this quantum of excrement is deprecated
        if ($plugin instanceof Plugin) {
            return $plugin;
        }

        if ($plugin instanceof PluginModel) {
            return $this->getPluginByName($plugin->getName());
        }

        return $this->getPluginByName($plugin);
    }

    /**
     * Gets the value of foundationPlugin from the record
     *
     * @return Plugin
     *
     * @deprecated since 4.0.0 - will be removed in 5.0.0
     */
    public function getFoundationPlugin()
    {
        \trigger_error(\sprintf(
            '%s is deprecated since version 4.0.0, to be removed in 5.0.0 without replacement.',
            __METHOD__
        ), \E_USER_DEPRECATED);

        if (!$this->foundationPlugin instanceof Plugin) {
            $this->foundationPlugin = $this->em
                ->getRepository(PluginModel::class)
                ->findOneBy([
                    'name'   => $this->getFoundationPluginName(),
                    'active' => true,
                ]);
        }

        return $this->foundationPlugin;
    }

    /**
     * @param Plugin        $plugin
     * @param UpdateContext $context
     *
     * @return array
     */
    private function getVersions(Plugin $plugin, UpdateContext $context)
    {
        $updatePath = $this->getPluginResource($plugin, '/Updates');

        if (isset($this->pluginVersions[$plugin->getName()])) {
            return $this->pluginVersions[$plugin->getName()];
        }

        /** @var \RecursiveDirectoryIterator $dirIterator */
        $dirIterator = new \RecursiveDirectoryIterator($updatePath, \RecursiveDirectoryIterator::SKIP_DOTS);

        /** @var \RecursiveIteratorIterator $iterator */
        $iterator = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            /**
             * @var \SplFileInfo $file
             */

            if ($file->isFile() && $file->isReadable() && 'php' === $file->getExtension()) {
                if ('AbstractUpdate.php' === $file->getFilename()) {
                    continue;
                }

                $version            = str_replace('_', '.', $file->getBasename('.php'));
                $versions[$version] = $file;
            }
        }

        uksort($versions, static function ($a, $b) {
            return version_compare($a, $b, '>');
        });

        $versions = array_filter(
            $versions,
            static function ($version) use ($context) {
                return version_compare($version, $context->getPlugin()->getVersion(), '>=');
            },
            ARRAY_FILTER_USE_KEY
        );

        $this->pluginVersions[$plugin->getName()] = $versions;

        return $versions;
    }
}
